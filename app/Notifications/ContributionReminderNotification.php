<?php

namespace App\Notifications;

use App\Models\Contribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContributionReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Contribution $contribution;

    public function __construct(Contribution $contribution)
    {
        $this->contribution = $contribution;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $balance = $this->contribution->amount_due - $this->contribution->amount_paid;

        return (new MailMessage)
            ->subject('⏰ Contribution Reminder - ' . $this->contribution->month . ' | ChamaHub')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a friendly reminder that you have an outstanding contribution.')
            ->line('**Month:** ' . $this->contribution->month)
            ->line('**Amount Due:** Ksh ' . number_format($this->contribution->amount_due, 2))
            ->line('**Amount Paid:** Ksh ' . number_format($this->contribution->amount_paid, 2))
            ->line('**Balance:** Ksh ' . number_format($balance, 2))
            ->action('Pay via M-Pesa', url('/dashboard'))
            ->line('Please clear your balance before the end of the month to avoid any penalties.')
            ->salutation('Regards, ChamaHub Management');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type'            => 'contribution_reminder',
            'title'           => 'Contribution Reminder',
            'message'         => 'Reminder: You have an unpaid contribution for ' . $this->contribution->month . '.',
            'contribution_id' => $this->contribution->id,
        ];
    }
}
