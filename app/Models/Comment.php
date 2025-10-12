<?php

namespace App\Models;

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
            $points = RewardHelper::POINTS_COMMENT;
            $postCreator = $comment->post->creator;

            // Award points to the post creator, but only if they aren't commenting on their own post
            if ($postCreator && $comment->user_id !== $postCreator->id) {
                $postCreator->increment('points', $points);
            }
        });

        // DEDUCT POINTS on deletion
        static::deleted(function (Comment $comment) {
            $points = RewardHelper::POINTS_COMMENT;
            $postCreator = $comment->post->creator;

            // Deduct points from the post creator
            if ($postCreator && $comment->user_id !== $postCreator->id) {
                $postCreator->decrement('points', $points);
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
