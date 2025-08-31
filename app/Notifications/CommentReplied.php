<?php

namespace App\Notifications;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentReplied extends Notification implements ShouldQueue, ShouldBroadcast
{
    use Queueable;

    protected $post;
    protected $original_comment;
    protected $replying_user;

    /**
     * Create a new notification instance.
     */
    public function __construct(Post $post, Comment $original_comment, User $replying_user)
    {
        $this->post = $post;
        $this->original_comment = $original_comment;
        $this->replying_user = $replying_user;
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
            ->subject('Your comment got a new reply!')
            ->line($this->replying_user->name . ' replied to your comment on the post "' . $this->post->title . '".')
            ->action('View Post', route('post.show', ['post' => $this->post->id]))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'post_id' => $this->post->id,
            'post_title' => $this->post->title,
            'original_comment_id' => $this->original_comment->id,
            'replying_user_id' => $this->replying_user->id,
            'replying_user_name' => $this->replying_user->name,
            'message' => $this->replying_user->name . ' replied to your comment on the post: "' . $this->post->title . '"'
        ];
    }

    /**
     * Get the broadcastable representation of the notification.
     */
    public function toBroadcast(object $notifiable): array
    {
        return [
            'message' => $this->replying_user->name . ' replied to your comment: "' . $this->original_comment->content . '"',
            'link' => route('post.show', ['post' => $this->post->id])
        ];
    }
}
