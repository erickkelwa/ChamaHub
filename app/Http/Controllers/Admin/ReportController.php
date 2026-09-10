<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use App\Models\Loan;
use App\Models\Meeting;
use App\Models\User;
use App\Notifications\ContributionReminderNotification;
use App\Notifications\DuesReminderNotification;
use App\Notifications\LoanApprovedNotification;
use App\Notifications\CustomResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
            ->whereHas('user', function ($query) {
                $query->where('role', 'member')->where('status', 'active');
            })
            ->get();

        $sentCount = 0;
        $failedCount = 0;

        foreach ($unpaid->groupBy('user_id') as $contributions) {
            $member = $contributions->first()->user;

            try {
                // One email per member, including every outstanding month.
                $member->notify(new DuesReminderNotification($contributions));

                $totalBalance = $contributions->sum(function ($contribution) {
                    return $contribution->amount_due - $contribution->amount_paid;
                });

                // Record the same reminder in the member's in-app notifications.
                \App\Models\Notification::create([
                    'user_id' => $member->id,
                    'type'    => 'dues_reminder',
                    'title'   => 'Contribution Dues Reminder',
                    'message' => 'Reminder: Your total outstanding contribution balance is Ksh ' . number_format($totalBalance, 2) . '.',
                    'is_read' => false,
                    'sent_at' => now(),
                ]);

                $sentCount++;
            } catch (\Throwable $exception) {
                $failedCount++;
                Log::error('Unable to send contribution dues reminder.', [
                    'user_id' => $member->id,
                    'email' => $member->email,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($failedCount > 0) {
            return back()->with('error', "Dues reminders were emailed to {$sentCount} member(s); {$failedCount} could not be sent. Check the application log.");
        }

        return back()->with('success', "Dues reminders emailed to {$sentCount} member(s).");
    }

    /**
     * Send a real test email (all 3 types) to the currently logged-in admin.
     * Useful for verifying SMTP and email templates on live/Render environments.
     */
    public function sendTestEmail()
    {
        $admin = auth()->user();

        $errors   = [];
        $sent     = [];

        // ── 1. Contribution Reminder ──────────────────────────────────────
        try {
            // Use a real contribution if one exists, otherwise build a demo object
            $contribution = Contribution::first();
            if (!$contribution) {
                $contribution = new Contribution([
                    'user_id'     => $admin->id,
                    'month'       => now()->format('M Y'),
                    'amount_due'  => 2000,
                    'amount_paid' => 500,
                    'status'      => 'partial',
                ]);
                $contribution->id = 0; // fake id so toDatabase() doesn't crash
            }
            $admin->notify(new ContributionReminderNotification($contribution));
            $sent[] = '📧 Contribution Reminder';
        } catch (\Exception $e) {
            Log::error('Test email - ContributionReminder failed', ['error' => $e->getMessage()]);
            $errors[] = 'Contribution Reminder: ' . $e->getMessage();
        }

        // ── 2. Loan Approved ──────────────────────────────────────────────
        try {
            $loan = Loan::first();
            if (!$loan) {
                $loan = new Loan([
                    'user_id'          => $admin->id,
                    'amount_requested' => 50000,
                    'amount_approved'  => 50000,
                    'interest_rate'    => 10,
                    'total_repayable'  => 55000,
                    'repayment_months' => 6,
                    'status'           => 'approved',
                ]);
                $loan->id = 0;
            } else {
                // Force approved values for the notification preview
                $loan->status           = 'approved';
                $loan->amount_approved  = $loan->amount_approved  ?? $loan->amount_requested ?? 50000;
                $loan->interest_rate    = $loan->interest_rate    ?? 10;
                $loan->total_repayable  = $loan->total_repayable  ?? ($loan->amount_approved * 1.10);
                $loan->repayment_months = $loan->repayment_months ?? 6;
            }
            $admin->notify(new LoanApprovedNotification($loan));
            $sent[] = '🎉 Loan Approved';
        } catch (\Exception $e) {
            Log::error('Test email - LoanApproved failed', ['error' => $e->getMessage()]);
            $errors[] = 'Loan Approved: ' . $e->getMessage();
        }

        // ── 3. Password Reset ─────────────────────────────────────────────
        try {
            $admin->notify(new CustomResetPasswordNotification('demo-test-token-123'));
            $sent[] = '🔐 Password Reset';
        } catch (\Exception $e) {
            Log::error('Test email - PasswordReset failed', ['error' => $e->getMessage()]);
            $errors[] = 'Password Reset: ' . $e->getMessage();
        }

        if (!empty($errors)) {
            $msg = 'Some emails failed: ' . implode(' | ', $errors);
            return back()->with('error', $msg);
        }

        $list = implode(', ', $sent);
        return back()->with('success',
            "✅ Test emails sent to {$admin->email} — {$list}. Check your inbox!"
        );
    }
}
