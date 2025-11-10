<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BoostedPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id', 'user_id', 'points_spent', 'boosted_until', 'status'
    ];

    protected $casts = [
        'boosted_until' => 'datetime'
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('boosted_until', '>', now());
    }
}
