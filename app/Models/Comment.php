<?php

namespace App\Models;

use App\Services\PointsService;
use App\Utils\RewardHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\Auth;

class Comment extends Model
{
    /** @use HasFactory<\Database\Factories\CommentFactory> */
    use HasFactory;

    use HasFactory;

    public $guarded = ['id'];

    protected $appends = ['is_flagged_by_current_user'];

    protected static function boot()
    {
        parent::boot();

        // AWARD POINTS on creation
        static::created(function (Comment $comment) {
            // Award points to comment author
            PointsService::awardPoints(
                $comment->user,
                $comment->parent_id ? 'comment_on_comment' : 'comment_created',
                $comment,
                "Commented on post"
            );

            // Award points to post author for receiving comment
            if (!$comment->parent_id) {
                PointsService::awardPoints(
                    $comment->post->creator,
                    'comment_received',
                    $comment,
                    "Received comment on post"
                );
            } else {
                // Award points to parent comment author
                PointsService::awardPoints(
                    $comment->parent->user,
                    'comment_on_comment_received',
                    $comment,
                    "Received reply to comment"
                );
            }
        });
    }

    public function getIsFlaggedByCurrentUserAttribute()
    {
        if (!Auth::check()) {
            return false;
        }

        $user_id = Auth::id();

        return $this->flags()
            ->where('flagged_by', $user_id)
            ->where('status', 'pending') // Optional: only consider pending flags
            ->exists();
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(Comment::class, 'parent_id')->with('replies.user');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'parent_id');
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function flags(): MorphMany
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }
}
