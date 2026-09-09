<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password reset token.
     */
    public string $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.' . config('auth.defaults.passwords') . '.expire', 120);

        return (new MailMessage)
            ->subject('🔐 Reset Your ChamaHub Password')
            ->greeting('Hello ' . $notifiable->name . '!')
            ->line('We received a request to reset the password for your **ChamaHub** account.')
            ->line('Click the button below to set a new password. This link will expire in **' . $expireMinutes . ' minutes**.')
            ->action('Reset My Password', $resetUrl)
            ->line('If you did not request a password reset, no further action is required — your account is safe.')
            ->line('For security, this link can only be used once.')
            ->salutation('— The ChamaHub Team');
    }
}
