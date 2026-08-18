<?php

namespace App\Http\Controllers;

use App\Models\Contribution;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function landing()
    {
        return view('landing');
    }

    public function dashboard()
    {
        $user = auth()->user();

        if ($user->role === 'admin' || $user->role === 'treasurer') {
            $totalMembers       = User::count();
            $totalContributions = Contribution::where('status', 'paid')->sum('amount_paid');
            $totalLoans         = Loan::where('status', 'approved')->sum('amount_approved');
            $pendingLoansCount  = Loan::where('status', 'pending')->count();

            return view('dashboard', compact(
                'totalMembers',
                'totalContributions',
                'totalLoans',
                'pendingLoansCount'
            ));
        }

        // Member-specific stats
        $myTotalSavings       = Contribution::where('user_id', $user->id)->where('status', 'paid')->sum('amount_paid');
        $myUnpaidDues         = Contribution::where('user_id', $user->id)->where('status', '!=', 'paid')->sum('amount_due')
                              - Contribution::where('user_id', $user->id)->where('status', '!=', 'paid')->sum('amount_paid');
        $myActiveLoan         = Loan::where('user_id', $user->id)->where('status', 'approved')->first();
        $myRecentContributions = Contribution::where('user_id', $user->id)->latest('month')->take(5)->get();
        $myUnpaidFines        = \App\Models\Fine::where('user_id', $user->id)->where('status', 'unpaid')->get();

        return view('member_dashboard', compact(
            'myTotalSavings',
            'myUnpaidDues',
            'myActiveLoan',
            'myRecentContributions',
            'myUnpaidFines'
        ));
    }
}
