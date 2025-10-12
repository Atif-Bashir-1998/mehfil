<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ContentRemovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Model $flaggable;
    public string $reason;

    /**
     * Create a new notification instance.
     */
    public function __construct(Model $flaggable, string $reason)
    {
        $this->flaggable = $flaggable;
        $this->reason = $reason;
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
        $contentType = class_basename($this->flaggable);

        return (new MailMessage)
            ->subject('Action Taken on Your Content: ' . $contentType . ' Removed')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->error() // Use the error style for high visibility
            ->line("We are writing to inform you that your recent **{$contentType}** has been removed (hidden) by a moderator.")
            ->line('This action was taken because the content was flagged by another user.')
            ->line('---')
            ->line('### Policy Violation Reason')
            ->line('Original reported reason: "' . $this->reason . '"')
            ->line('---')
            ->line('Please review our community guidelines to ensure future posts comply with our rules.')
            ->line('If you believe this was an error, please contact our support team.');
    }
}
