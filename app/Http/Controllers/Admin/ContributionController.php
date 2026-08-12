<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\User;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
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
        return view('admin.contributions.index', compact('contributions'));
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
}
