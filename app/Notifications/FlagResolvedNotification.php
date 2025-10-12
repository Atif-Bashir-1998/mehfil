<?php

namespace App\Notifications;

use App\Models\Flag;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class FlagResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public Flag $flag;

    /**
     * Create a new notification instance.
     */
    public function __construct(Flag $flag)
    {
        $this->flag = $flag;
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
        $status = ucfirst($this->flag->status);
        $adminNotes = $this->flag->admin_notes ?? 'No specific notes were added by the moderator.';
        $flaggableType = class_basename($this->flag->flaggable_type);

        return (new MailMessage)
            ->subject('Update: Your Report on ' . $flaggableType . ' Has Been Resolved')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for helping us moderate the community. We have reviewed your report and taken action.')
            ->line("---")
            ->line("### Resolution Details")
            ->line("Content Type: **{$flaggableType}**")
            ->line("Report Status: **{$status}**")
            ->line('Moderator Notes: ' . $adminNotes)
            ->line("---")
            ->line('Your contribution is valuable. Please continue to report content that violates our guidelines.');
    }
}
