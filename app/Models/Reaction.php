<?php

namespace App\Models;

use App\Enums\ReactionType;
use App\Utils\RewardHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reaction extends Model
{
    /** @use HasFactory<\Database\Factories\ReactionsFactory> */
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = [
        'type' => ReactionType::class,
    ];

    protected static function boot()
    {
        parent::boot();

        // AWARD POINTS on creation
        static::created(function (Reaction $reaction) {
            $points = RewardHelper::POINTS_REACTION;
            $postCreator = $reaction->post->creator;

            // Award points to the post creator, but only if they aren't reacting to their own post
            if ($postCreator && $reaction->user_id !== $postCreator->id) {
                $postCreator->increment('points', $points);
            }
        });

        // DEDUCT POINTS on deletion
        static::deleted(function (Reaction $reaction) {
            $points = RewardHelper::POINTS_REACTION;
            $postCreator = $reaction->post->creator;

            // Deduct points from the post creator
            if ($postCreator && $reaction->user_id !== $postCreator->id) {
                $postCreator->decrement('points', $points);
            }
        });
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
