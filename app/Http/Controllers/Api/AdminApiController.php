<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\Meeting;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\Poll;
use App\Models\User;
use App\Services\MpesaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class AdminApiController extends Controller
{
    protected MpesaService $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    // ── Users (Members) CRUD ──────────────────────────────────────────────────

    public function index(Request $request)
    {
        // This method is shared by apiResource; route name differentiates context.
        // Default: list users.
        $query = User::withSum(['contributions as total_savings' => function ($q) {
            $q->where('status', 'paid');
        }], 'amount_paid');

        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%")
                  ->orWhere('phone', 'like', "%$search%");
            });
        }

        return response()->json($query->paginate(20));
    }

    public function show($id)
    {
        $user = User::withSum(['contributions as total_savings' => fn($q) => $q->where('status', 'paid')], 'amount_paid')
            ->findOrFail($id);
        return response()->json($user);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required|string|max:15',
            'role'     => 'required|in:admin,treasurer,member',
            'status'   => 'required|in:active,inactive',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $user = User::create($validated);
        return response()->json(['message' => 'Member created.', 'user' => $user], 201);
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'   => 'sometimes|string|max:255',
            'email'  => "sometimes|email|unique:users,email,$id",
            'phone'  => 'sometimes|string|max:15',
            'role'   => 'sometimes|in:admin,treasurer,member',
            'status' => 'sometimes|in:active,inactive',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => ['confirmed', Rules\Password::defaults()]]);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);
        return response()->json(['message' => 'Member updated.', 'user' => $user]);
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Member deleted.']);
    }

    // ── Loans – Approve / Reject ──────────────────────────────────────────────

    public function approveLoan(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $request->validate(['amount_approved' => 'required|numeric|min:1']);

        $interestRate = $loan->interest_rate ?? 5;
        $totalRepayable = $request->amount_approved * (1 + $interestRate / 100);

        $loan->update([
            'status'          => 'approved',
            'amount_approved' => $request->amount_approved,
            'total_repayable' => $totalRepayable,
            'balance'         => $totalRepayable,
            'approved_by'     => $request->user()->id,
            'approved_at'     => now(),
        ]);

        Notification::create([
            'user_id' => $loan->user_id,
            'type'    => 'loan_approved',
            'title'   => 'Loan Approved',
            'message' => "Your loan of KES " . number_format($request->amount_approved, 2) . " has been approved.",
            'sent_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Loan approved.', 'loan' => $loan]);
    }

    public function rejectLoan(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        $request->validate(['rejection_note' => 'required|string|max:500']);

        $loan->update([
            'status'         => 'rejected',
            'rejection_note' => $request->rejection_note,
        ]);

        Notification::create([
            'user_id' => $loan->user_id,
            'type'    => 'loan_rejected',
            'title'   => 'Loan Rejected',
            'message' => "Your loan application was rejected. Reason: {$request->rejection_note}",
            'sent_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Loan rejected.', 'loan' => $loan]);
    }

    // ── Meetings – Attendance ─────────────────────────────────────────────────

    public function recordAttendance(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        $request->validate([
            'attendances'           => 'required|array',
            'attendances.*.user_id' => 'required|exists:users,id',
            'attendances.*.status'  => 'required|in:present,absent,excused',
        ]);

        foreach ($request->attendances as $att) {
            \App\Models\MeetingAttendance::updateOrCreate(
                ['meeting_id' => $meeting->id, 'user_id' => $att['user_id']],
                ['status' => $att['status']]
            );
        }

        return response()->json(['message' => 'Attendance recorded.']);
    }

    // ── Decisions / Polls ─────────────────────────────────────────────────────

    public function closeDecision(Request $request, $id)
    {
        $poll = Poll::findOrFail($id);
        $poll->update(['status' => 'closed']);
        return response()->json(['message' => 'Poll closed.', 'poll' => $poll]);
    }

    // ── Dividends ─────────────────────────────────────────────────────────────

    public function dividends(Request $request)
    {
        $totalFund = Contribution::where('status', 'paid')->sum('amount_paid');
        $members   = User::withSum(['contributions as savings' => fn($q) => $q->where('status', 'paid')], 'amount_paid')->get();

        $result = $members->map(function ($m) use ($totalFund) {
            $share = $totalFund > 0 ? ($m->savings / $totalFund) * 100 : 0;
            return [
                'id'       => $m->id,
                'name'     => $m->name,
                'savings'  => (float) $m->savings,
                'share_pct'=> round($share, 2),
            ];
        });

        return response()->json(['total_fund' => $totalFund, 'members' => $result]);
    }

    // ── Reports ───────────────────────────────────────────────────────────────

    public function reports(Request $request)
    {
        $totalMembers        = User::count();
        $totalContributions  = Contribution::where('status', 'paid')->sum('amount_paid');
        $totalLoans          = Loan::where('status', 'approved')->sum('amount_approved');
        $totalRepaid         = Loan::sum('amount_repaid');
        $totalFines          = Fine::sum('amount');
        $totalFinesPaid      = Fine::where('status', 'paid')->sum('amount');

        return response()->json([
            'total_members'       => $totalMembers,
            'total_contributions' => (float) $totalContributions,
            'total_loans'         => (float) $totalLoans,
            'total_repaid'        => (float) $totalRepaid,
            'total_fines'         => (float) $totalFines,
            'total_fines_paid'    => (float) $totalFinesPaid,
        ]);
    }

    // ── Bulk STK Push ─────────────────────────────────────────────────────────

    public function bulkStkPush(Request $request)
    {
        $request->validate([
            'month'  => 'required|string|max:20',
            'target' => 'required|in:unpaid,all',
        ]);

        $query = Contribution::with('user')->where('month', $request->month);
        if ($request->target === 'unpaid') {
            $query->whereIn('status', ['unpaid', 'partial']);
        }

        $contributions = $query->get();
        $success = $failed = $noPhone = 0;

        foreach ($contributions as $contribution) {
            $user = $contribution->user;
            if (!$user || empty($user->phone)) { $noPhone++; continue; }

            $raw = preg_replace('/[^0-9]/', '', $user->phone);
            $phone = str_starts_with($raw, '0') ? '254' . substr($raw, 1) : (str_starts_with($raw, '254') ? $raw : null);
            if (!$phone) { $noPhone++; continue; }

            $due = (float) ($contribution->amount_due - $contribution->amount_paid);
            if ($due <= 0) continue;

            $result = $this->mpesa->stkPush($phone, $due, 'Chama-' . $contribution->id, 'Contribution ' . substr($contribution->month, 0, 10));
            if (!empty($result['success'])) {
                Payment::create([
                    'user_id'         => $user->id,
                    'payable_type'    => Contribution::class,
                    'payable_id'      => $contribution->id,
                    'amount'          => $due,
                    'payment_method'  => 'mpesa',
                    'status'          => 'pending',
                    'mpesa_reference' => $result['data']['CheckoutRequestID'] ?? null,
                    'phone_number'    => $phone,
                ]);
                $success++;
            } else {
                $failed++;
            }
        }

        return response()->json([
            'message'  => "STK pushed to $success member(s). Failed: $failed. Skipped (no phone): $noPhone.",
            'success'  => $success,
            'failed'   => $failed,
            'no_phone' => $noPhone,
        ]);
    }
}
