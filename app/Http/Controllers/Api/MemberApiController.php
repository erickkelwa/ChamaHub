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
use App\Models\Vote;
use App\Services\MpesaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class MemberApiController extends Controller
{
    protected MpesaService $mpesa;

    public function __construct(MpesaService $mpesa)
    {
        $this->mpesa = $mpesa;
    }

    // ── Profile ──────────────────────────────────────────────────────────────

    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'         => 'sometimes|string|max:255',
            'phone'        => 'sometimes|string|max:20',
            'password'     => 'sometimes|confirmed|min:8',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json(['message' => 'Profile updated', 'user' => $user]);
    }

    // ── Contributions ─────────────────────────────────────────────────────────

    public function contributions(Request $request)
    {
        $contributions = Contribution::where('user_id', $request->user()->id)
            ->latest('month')
            ->paginate(20);

        return response()->json($contributions);
    }

    // ── Loans ─────────────────────────────────────────────────────────────────

    public function listLoans(Request $request)
    {
        $loans = Loan::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(20);

        return response()->json($loans);
    }

    public function applyLoan(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'amount_requested' => 'required|numeric|min:100',
            'repayment_months' => 'required|integer|min:1|max:24',
            'purpose'          => 'required|string|max:500',
        ]);

        // Check for unpaid fines
        $unpaidFines = Fine::where('user_id', $user->id)->where('status', 'unpaid')->sum('amount');
        if ($unpaidFines > 0) {
            return response()->json(['message' => 'You have unpaid fines. Please settle them first.'], 422);
        }

        // Check for active / pending loans
        $activeLoan = Loan::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'approved'])->first();
        if ($activeLoan) {
            return response()->json(['message' => 'You already have an active or pending loan.'], 422);
        }

        // 3× savings limit
        $totalSavings = Contribution::where('user_id', $user->id)
            ->where('status', 'paid')->sum('amount_paid');
        $maxLimit = $totalSavings * 3;

        if ($maxLimit <= 0) {
            return response()->json(['message' => 'You must have at least one paid contribution.'], 422);
        }
        if ($request->amount_requested > $maxLimit) {
            return response()->json([
                'message' => "Amount exceeds your limit of KES " . number_format($maxLimit, 2),
            ], 422);
        }

        $loan = Loan::create([
            'user_id'          => $user->id,
            'amount_requested' => $request->amount_requested,
            'repayment_months' => $request->repayment_months,
            'reason'           => $request->purpose,
            'status'           => 'pending',
            'interest_rate'    => 5,
        ]);

        // Notify admins
        $admins = \App\Models\User::whereIn('role', ['admin', 'treasurer'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type'    => 'loan_application',
                'title'   => 'New Loan Application',
                'message' => "{$user->name} applied for KES " . number_format($request->amount_requested, 2),
                'sent_at' => Carbon::now(),
            ]);
        }

        return response()->json(['message' => 'Loan application submitted.', 'loan' => $loan], 201);
    }

    // ── Meetings ──────────────────────────────────────────────────────────────

    public function meetings(Request $request)
    {
        $meetings = Meeting::with('creator')->latest('meeting_date')->paginate(20);
        return response()->json($meetings);
    }

    public function meetingDetail(Request $request, $id)
    {
        $meeting = Meeting::with('creator')->findOrFail($id);
        return response()->json($meeting);
    }

    // ── Decisions / Polls ─────────────────────────────────────────────────────

    public function decisions(Request $request)
    {
        $polls = Poll::with(['creator', 'votes'])->latest()->paginate(20);
        return response()->json($polls);
    }

    public function vote(Request $request, $id)
    {
        $user = $request->user();
        $poll = Poll::findOrFail($id);

        if ($poll->status !== 'open') {
            return response()->json(['message' => 'This poll is closed.'], 422);
        }

        $request->validate(['choice' => 'required|in:yes,no,abstain']);

        $existingVote = Vote::where('poll_id', $id)->where('user_id', $user->id)->first();
        if ($existingVote) {
            return response()->json(['message' => 'You have already voted.'], 422);
        }

        $vote = Vote::create([
            'poll_id' => $id,
            'user_id' => $user->id,
            'vote'    => $request->choice,
        ]);

        return response()->json(['message' => 'Vote recorded.', 'vote' => $vote], 201);
    }

    // ── Notifications ─────────────────────────────────────────────────────────

    public function notifications(Request $request)
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest('sent_at')
            ->paginate(30);

        return response()->json($notifications);
    }

    // ── M-Pesa ────────────────────────────────────────────────────────────────

    public function mpesaPush(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'contribution_id' => 'required|exists:contributions,id',
            'phone'           => 'sometimes|string',
        ]);

        $contribution = Contribution::findOrFail($request->contribution_id);
        $amountDue = $contribution->amount_due - $contribution->amount_paid;

        if ($amountDue <= 0) {
            return response()->json(['message' => 'This contribution is already fully paid.'], 422);
        }

        $phone = $request->phone ?? $user->phone;
        $phone = preg_replace('/^0/', '254', $phone);

        $result = $this->mpesa->stkPush(
            $phone,
            $amountDue,
            'ChamaHub-' . $contribution->id,
            'Contribution for ' . $contribution->month
        );

        if ($result['success']) {
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
            return response()->json(['message' => 'STK Push sent. Enter your M-Pesa PIN.']);
        }

        return response()->json(['message' => 'M-Pesa failed: ' . $result['message']], 502);
    }
}
