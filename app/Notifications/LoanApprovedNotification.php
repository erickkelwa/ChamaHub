<?php

namespace App\Notifications;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LoanApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Loan $loan;

    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('🎉 Your Loan Has Been Approved - ChamaHub')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Great news! Your loan application has been **approved**.')
            ->line('**Amount Approved:** Ksh ' . number_format($this->loan->amount_approved, 2))
            ->line('**Interest Rate:** ' . $this->loan->interest_rate . '%')
            ->line('**Total Repayable:** Ksh ' . number_format($this->loan->total_repayable, 2))
            ->line('**Repayment Duration:** ' . $this->loan->repayment_months . ' months')
            ->action('View Loan Details', url('/dashboard'))
            ->line('Please ensure timely repayments to maintain a good credit standing within the group.')
            ->salutation('Regards, ChamaHub Management');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'    => 'loan_approved',
            'title'   => 'Loan Approved',
            'message' => 'Your loan of Ksh ' . number_format($this->loan->amount_approved, 2) . ' has been approved.',
            'loan_id' => $this->loan->id,
        ];
    }
}
