<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UserAccountRestrictedNotification extends Notification
{
    use Queueable;

    protected string $reason;

    /**
     * Create a new notification instance.
     *
     * @param string $reason The reason for the account restriction.
     */
    public function __construct(string $reason)
    {
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
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
        return (new MailMessage)
            ->subject('Important: Your Account Has Been Restricted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('We are writing to inform you that your account on our platform has been restricted due to a violation of our Community Guidelines.')
            ->line('Your profile and content are currently **hidden** from public view.')
            ->line("The specific reason for this action, based on the latest report, is: **\"{$this->reason}\"**")
            ->line('If you believe this action was taken in error, please reply to this email to contact our moderation team and submit an appeal.')
            ->action('View Community Guidelines', url('/community-guidelines'))
            ->line('We appreciate your understanding as we work to maintain a safe and positive environment for everyone.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'Your account has been restricted.',
            'reason' => $this->reason,
        ];
    }
}
