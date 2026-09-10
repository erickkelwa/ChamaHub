<?php

use App\Models\User;
use App\Models\Contribution;
use App\Models\Loan;
use App\Notifications\ContributionReminderNotification;
use App\Notifications\LoanApprovedNotification;
use App\Notifications\CustomResetPasswordNotification;

echo "\n========================================\n";
echo "  ChamaHub Email Notification Test\n";
echo "========================================\n\n";

// Find target user
$user = User::first();
if (!$user) { echo "❌ No users in DB.\n"; exit(1); }
echo "✅ Target user : {$user->name}\n";
echo "✅ Target email: {$user->email}\n\n";

// ── TEST 1: Contribution Reminder ────────────────────────────────────────
echo "📧 TEST 1 — Contribution Reminder\n";
echo "   ─────────────────────────────────\n";

// Try real unpaid contribution first, else use real one from DB or skip to fake
$contribution = Contribution::where('user_id', $user->id)->first()
             ?? Contribution::first();

if ($contribution) {
    echo "   Using DB contribution: {$contribution->month} | Due: Ksh " . number_format($contribution->amount_due, 2) . "\n";
    try {
        $user->notify(new ContributionReminderNotification($contribution));
        echo "   ✅ SUCCESS — Email dispatched to {$user->email}\n\n";
    } catch (\Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ⚠️  No contribution records in DB — skipping (seed data first)\n\n";
}

// ── TEST 2: Loan Approved ─────────────────────────────────────────────────
echo "📧 TEST 2 — Loan Approved\n";
echo "   ─────────────────────────────────\n";

$loan = Loan::where('user_id', $user->id)->first()
     ?? Loan::first();

if ($loan) {
    // Temporarily set approved values if not already approved
    $loan->status            = 'approved';
    $loan->amount_approved   = $loan->amount_approved ?? $loan->amount_requested ?? 50000;
    $loan->interest_rate     = $loan->interest_rate ?? 10;
    $loan->total_repayable   = $loan->total_repayable ?? ($loan->amount_approved * 1.1);
    $loan->repayment_months  = $loan->repayment_months ?? 6;

    echo "   Using DB loan: Ksh " . number_format($loan->amount_approved, 2) . " at {$loan->interest_rate}%\n";
    try {
        $user->notify(new LoanApprovedNotification($loan));
        echo "   ✅ SUCCESS — Email dispatched to {$user->email}\n\n";
    } catch (\Exception $e) {
        echo "   ❌ FAILED: " . $e->getMessage() . "\n\n";
    }
} else {
    echo "   ⚠️  No loan records in DB — skipping (seed data first)\n\n";
}

// ── TEST 3: Password Reset ─────────────────────────────────────────────────
echo "📧 TEST 3 — Password Reset Email\n";
echo "   ─────────────────────────────────\n";
try {
    $user->notify(new CustomResetPasswordNotification('fake-test-token-abc123'));
    echo "   ✅ SUCCESS — Password reset email dispatched to {$user->email}\n\n";
} catch (\Exception $e) {
    echo "   ❌ FAILED: " . $e->getMessage() . "\n\n";
}

// ── SUMMARY ───────────────────────────────────────────────────────────────
echo "========================================\n";
echo "  🏁 Test complete!\n";
echo "  📬 Check inbox: {$user->email}\n";
echo "  💡 Also check storage/logs/laravel.log for any SMTP errors\n";
echo "========================================\n\n";
