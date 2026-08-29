<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Payment;
use App\Models\User;
use App\Services\MpesaService;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    protected MpesaService $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    /**
     * Display a listing of the contributions.
     */
    public function index(Request $request)
    {
        $query = Contribution::with('user');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            })->orWhere('month', 'like', "%{$search}%");
        }

        $contributions = $query->latest()->paginate(15);
        $availableMonths = Contribution::select('month')->distinct()->pluck('month');
        return view('admin.contributions.index', compact('contributions', 'availableMonths'));
    }

    /**
     * Show the form for creating a new contribution record.
     */
    public function create()
    {
        $members = User::whereIn('role', ['member', 'treasurer', 'admin'])->get();
        return view('admin.contributions.create', compact('members'));
    }

    /**
     * Store a newly created contribution in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string|max:20',
            'amount_due' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,partial,paid',
        ]);

        $data = $request->all();
        if ($data['status'] == 'paid' && empty($data['paid_at'])) {
            $data['paid_at'] = now();
        }

        Contribution::create($data);

        return redirect()->route('admin.contributions.index')->with('success', 'Contribution record created successfully.');
    }

    /**
     * Show the form for editing the specified contribution.
     */
    public function edit($id)
    {
        $contribution = Contribution::findOrFail($id);
        $members = User::all();
        return view('admin.contributions.edit', compact('contribution', 'members'));
    }

    /**
     * Update the specified contribution in storage.
     */
    public function update(Request $request, $id)
    {
        $contribution = Contribution::findOrFail($id);

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string|max:20',
            'amount_due' => 'required|numeric|min:0',
            'amount_paid' => 'required|numeric|min:0',
            'status' => 'required|in:unpaid,partial,paid',
        ]);

        $data = $request->all();
        if ($data['status'] == 'paid' && !$contribution->paid_at) {
            $data['paid_at'] = now();
        } elseif ($data['status'] != 'paid') {
            $data['paid_at'] = null;
        }

        $contribution->update($data);

        return redirect()->route('admin.contributions.index')->with('success', 'Contribution updated successfully.');
    }

    /**
     * Remove the specified contribution from storage.
     */
    public function destroy($id)
    {
        $contribution = Contribution::findOrFail($id);
        $contribution->delete();

        return redirect()->route('admin.contributions.index')->with('success', 'Contribution deleted successfully.');
    }

    /**
     * Automatically generate monthly contribution dues for all members in 1 click.
     */
    public function generateSchedule(Request $request)
    {
        $request->validate([
            'month'      => 'required|string|max:20',
            'amount_due' => 'required|numeric|min:1',
        ]);

        $month = substr(trim($request->month), 0, 20);
        $amountDue = (float) $request->amount_due;

        $members = User::all();
        $createdCount = 0;
        $skippedCount = 0;

        foreach ($members as $member) {
            $existing = Contribution::where('user_id', $member->id)
                ->where('month', $month)
                ->first();

            if (!$existing) {
                Contribution::create([
                    'user_id'     => $member->id,
                    'month'       => $month,
                    'amount_due'  => $amountDue,
                    'amount_paid' => 0,
                    'status'      => 'unpaid',
                ]);
                $createdCount++;
            } else {
                $skippedCount++;
            }
        }

        $msg = "Generated Ksh " . number_format($amountDue, 2) . " dues for {$createdCount} members for '{$month}'.";
        if ($skippedCount > 0) {
            $msg .= " ({$skippedCount} members already had a record for this month).";
        }

        return redirect()->route('admin.contributions.index')->with('success', $msg);
    }

    /**
     * Dispatch bulk M-Pesa STK Push prompts to members for a specific month.
     */
    public function bulkStkPush(Request $request)
    {
        $request->validate([
            'month'  => 'required|string|max:20',
            'target' => 'required|in:unpaid,all',
        ]);

        $month  = trim($request->month);
        $target = $request->target;

        $query = Contribution::with('user')->where('month', $month);

        if ($target === 'unpaid') {
            $query->whereIn('status', ['unpaid', 'partial']);
        }

        $contributions = $query->get();

        if ($contributions->isEmpty()) {
            return redirect()->route('admin.contributions.index')
                ->with('error', "No contribution records found for month '{$month}'.");
        }

        $successCount = 0;
        $failedCount  = 0;
        $noPhoneCount = 0;

        foreach ($contributions as $contribution) {
            $user = $contribution->user;
            if (!$user || empty($user->phone)) {
                $noPhoneCount++;
                continue;
            }

            // Normalize phone number to 2547XXXXXXXX or 2541XXXXXXXX
            $rawPhone = preg_replace('/[^0-9]/', '', $user->phone);
            if (str_starts_with($rawPhone, '0')) {
                $phone = '254' . substr($rawPhone, 1);
            } elseif (str_starts_with($rawPhone, '254')) {
                $phone = $rawPhone;
            } else {
                $noPhoneCount++;
                continue;
            }

            $amountDue = (float) ($contribution->amount_due - $contribution->amount_paid);
            if ($amountDue <= 0) {
                continue; // already paid
            }

            $result = $this->mpesa->stkPush(
                $phone,
                $amountDue,
                'Chama-' . $contribution->id,
                'Contribution ' . substr($contribution->month, 0, 10)
            );

            if (!empty($result['success'])) {
                Payment::create([
                    'user_id'         => $user->id,
                    'payable_type'    => Contribution::class,
                    'payable_id'      => $contribution->id,
                    'amount'          => $amountDue,
                    'payment_method'  => 'mpesa',
                    'status'          => 'pending',
                    'mpesa_reference' => $result['data']['CheckoutRequestID'] ?? null,
                    'phone_number'    => $phone,
                ]);
                $successCount++;
            } else {
                $failedCount++;
            }
        }

        $msg = "M-Pesa STK Push prompt sent to {$successCount} member(s) for '{$month}'.";
        if ($failedCount > 0) {
            $msg .= " ({$failedCount} failed to dispatch).";
        }
        if ($noPhoneCount > 0) {
            $msg .= " ({$noPhoneCount} member(s) skipped due to missing or invalid phone number).";
        }

        return redirect()->route('admin.contributions.index')
            ->with($successCount > 0 ? 'success' : 'warning', $msg);
    }
}
