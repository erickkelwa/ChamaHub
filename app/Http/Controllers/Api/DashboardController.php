<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Fine;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if (in_array($user->role, ['admin', 'treasurer'])) {
            return response()->json([
                'role'               => $user->role,
                'total_members'      => User::count(),
                'total_contributions'=> Contribution::where('status', 'paid')->sum('amount_paid'),
                'total_loans'        => Loan::where('status', 'approved')->sum('amount_approved'),
                'pending_loans'      => Loan::where('status', 'pending')->count(),
            ]);
        }

        // Member dashboard
        $myTotalSavings = Contribution::where('user_id', $user->id)
            ->where('status', 'paid')->sum('amount_paid');

        $unpaidBalance = Contribution::where('user_id', $user->id)
            ->where('status', '!=', 'paid')
            ->selectRaw('SUM(amount_due) - SUM(amount_paid) as balance')
            ->value('balance') ?? 0;

        $activeLoan = Loan::where('user_id', $user->id)
            ->where('status', 'approved')
            ->first();

        $recentContributions = Contribution::where('user_id', $user->id)
            ->latest('month')->take(5)->get();

        $unpaidFines = Fine::where('user_id', $user->id)
            ->where('status', 'unpaid')->get();

        return response()->json([
            'role'                => 'member',
            'total_savings'       => (float) $myTotalSavings,
            'unpaid_balance'      => (float) $unpaidBalance,
            'active_loan'         => $activeLoan,
            'recent_contributions'=> $recentContributions,
            'unpaid_fines'        => $unpaidFines,
        ]);
    }
}
