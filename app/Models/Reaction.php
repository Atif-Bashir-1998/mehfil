<?php

namespace App\Models;

use App\Enums\ReactionType;
use App\Services\PointsService;
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
            // Award points to reactor
            PointsService::awardPoints(
                $reaction->user,
                'post_reacted',
                $reaction,
                "Reacted to post"
            );

            // Award points to post author for receiving reaction (if not own post)
            if ($reaction->user_id !== $reaction->post->created_by) {
                PointsService::awardPoints(
                    $reaction->post->creator,
                    'reaction_received',
                    $reaction,
                    "Received reaction on post"
                );
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
