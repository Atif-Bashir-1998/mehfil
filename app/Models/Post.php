<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    /** @use HasFactory<\Database\Factories\PostFactory> */
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = [
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = ['is_flagged_by_current_user'];

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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // comments on the post
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->whereNull('parent_id');
    }

    // comments on post + comments on comments
    public function all_comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(Reaction::class);
    }

    // Multiple images for posts
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable')->orderBy('order');
    }

    public function flags(): MorphMany
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }
}
