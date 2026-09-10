<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class DuesReminderNotification extends Notification
{
    public function __construct(private Collection $contributions)
    {
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        $totalBalance = $this->contributions->sum(function ($contribution) {
            return $contribution->amount_due - $contribution->amount_paid;
        });

        $message = (new MailMessage)
            ->subject('Contribution Dues Reminder | ChamaHub')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('This is a reminder that you have outstanding contribution dues:');

        foreach ($this->contributions as $contribution) {
            $balance = $contribution->amount_due - $contribution->amount_paid;
            $message->line($contribution->month . ': Ksh ' . number_format($balance, 2) . ' outstanding');
        }

        return $message
            ->line('**Total outstanding:** Ksh ' . number_format($totalBalance, 2))
            ->action('View and pay dues', url('/dashboard'))
            ->line('Please clear your balance before the end of the month to avoid penalties.')
            ->salutation('Regards, ChamaHub Management');
    }
}
