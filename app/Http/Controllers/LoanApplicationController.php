<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class LoanApplicationController extends Controller
{
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'amount_requested' => 'required|numeric|min:100',
            'repayment_months' => 'required|integer|min:1|max:24',
            'purpose' => 'required|string|max:500',
        ]);

        // Condition 1: No Unpaid Fines
        $unpaidFines = Fine::where('user_id', $user->id)->where('status', 'unpaid')->sum('amount');
        if ($unpaidFines > 0) {
            return redirect()->back()->with('error', 'You have unpaid fines. Please settle them before applying for a loan.');
        }

        // Condition 2: No Active Loans
        $activeLoan = Loan::where('user_id', $user->id)->whereIn('status', ['pending', 'approved'])->first();
        if ($activeLoan) {
            return redirect()->back()->with('error', 'You already have an active or pending loan. You cannot apply for a new one until it is settled.');
        }

        // Condition 3: Max Loan Limit (3x Savings)
        $totalSavings = Contribution::where('user_id', $user->id)->where('status', 'paid')->sum('amount_paid');
        $maxLoanLimit = $totalSavings * 3;

        if ($request->amount_requested > $maxLoanLimit) {
            return redirect()->back()->with('error', "Your requested amount exceeds your maximum limit of Ksh " . number_format($maxLoanLimit, 2) . " (3x your savings).");
        }

        if ($maxLoanLimit <= 0) {
            return redirect()->back()->with('error', "You must have at least one paid contribution to apply for a loan.");
        }

        // Create Loan Application
        $loan = Loan::create([
            'user_id' => $user->id,
            'amount_requested' => $request->amount_requested,
            'repayment_months' => $request->repayment_months,
            'purpose' => $request->purpose,
            'status' => 'pending',
            'interest_rate' => 5, // Default interest, admin can change
        ]);

        // Notify Admins
        $admins = User::whereIn('role', ['admin', 'treasurer'])->get();
        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'type' => 'loan_application',
                'title' => 'New Loan Application',
                'message' => "{$user->name} has applied for a loan of Ksh " . number_format($request->amount_requested, 2) . ".",
                'sent_at' => Carbon::now(),
            ]);
        }

        return redirect()->back()->with('success', 'Your loan application has been submitted successfully and is pending approval.');
    }
}
