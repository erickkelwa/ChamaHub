<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Loan;
use App\Models\Meeting;
use App\Models\User;
use App\Notifications\ContributionReminderNotification;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Show the reports overview page.
     */
    public function index()
    {
        $totalMembers         = User::where('status', 'active')->count();
        $paidContributions    = Contribution::where('status', 'paid')->sum('amount_paid');
        $unpaidContributions  = Contribution::where('status', 'unpaid')->sum('amount_due');
        $activeLoansValue     = Loan::where('status', 'approved')->sum('balance');
        $totalLoansDisbursed  = Loan::where('status', '!=', 'rejected')->sum('amount_approved');
        $meetingsThisYear     = Meeting::whereYear('meeting_date', now()->year)->count();

        // Per-month contribution breakdown (last 12 months)
        $contributionsByMonth = Contribution::selectRaw("month, SUM(amount_paid) as total_paid, SUM(amount_due) as total_due")
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.index', compact(
            'totalMembers', 'paidContributions', 'unpaidContributions',
            'activeLoansValue', 'totalLoansDisbursed', 'meetingsThisYear',
            'contributionsByMonth'
        ));
    }

    /**
     * Send contribution reminders to all members with unpaid contributions.
     */
    public function sendReminders()
    {
        $unpaid = Contribution::with('user')
            ->where('status', '!=', 'paid')
            ->get();

        foreach ($unpaid as $contribution) {
            if ($contribution->user) {
                // Record in-app notification
                \App\Models\Notification::create([
                    'user_id' => $contribution->user->id,
                    'type'    => 'contribution_reminder',
                    'title'   => 'Contribution Reminder - ' . $contribution->month,
                    'message' => 'Reminder: You have an unpaid contribution balance of Ksh ' . number_format($contribution->amount_due - $contribution->amount_paid, 2) . ' for ' . $contribution->month . '.',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);

                // Send email notification
                $contribution->user->notify(new ContributionReminderNotification($contribution));
            }
        }

        return back()->with('success', 'Contribution reminders sent to ' . $unpaid->count() . ' member(s).');
    }
}
