<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComment extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $post;
    protected $commenting_user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, User $commenting_user)
    {
        $this->post = $post;
        $this->commenting_user = $commenting_user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'mail', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your post got a new comment!')
            ->line($this->commenting_user->name . ' commented on your post post "' . $this->post->title . ' .')
            ->action('View post', route('post.show', ['post' => $this->post->id]))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        // This data will be saved to the database for the notifications page
        return [
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'commenting_user_id' => $this->commenting_user->id,
            'commenting_user_name' => $this->commenting_user->name,
            'message' => $this->commenting_user->name . ' liked your post: "' . $this->post->title . '"'
        ];
    }

    public function toBroadcast(object $notifiable): array
    {
        // This data will be broadcasted to the frontend for real-time notifications
        return [
            'message' => $this->commenting_user->name . ' commented on your post post "' . $this->post->title . ' .',
            'link' => route('post.show', ['post' => $this->post->id])
        ];
    }
}
