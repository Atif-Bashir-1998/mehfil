<?php

namespace App\Models;

use App\Notifications\UserAccountRestrictedNotification;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Hasroles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'slogan',
        'occupation',
        'location',
        'is_hidden'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected $with = ['profile_image', 'cover_image'];

    protected static function boot()
    {
        parent::boot();

        static::updated(function (User $user) {
            // Check if 'is_hidden' was just set to true, and it wasn't true before.
            if ($user->wasChanged('is_hidden') && $user->is_hidden === true) {
                // Find the reason from the most recent, unresolved flag against this user.
                // We assume the action that hides the user also resolves the flag,
                // so we look for the most recent flag on the user.
                $flag = Flag::where('flaggable_type', get_class($user))
                            ->where('flaggable_id', $user->id)
                            ->latest()
                            ->first();

                // If a flag reason is found, send the notification
                $reason = $flag ? $flag->reason : 'General community guidelines violation.';

                $user->notify(new UserAccountRestrictedNotification($reason));
            }
        });
    }

    public function posts()
    {
        return $this->hasMany(Post::class, 'created_by');
    }

    /**
     * Get all of the images for the user.
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    /**
     * Get the profile image for the user.
     */
    public function profile_image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'profile_image_id');
    }

    /**
     * Get the cover image for the user.
     */
    public function cover_image(): HasOne
    {
        return $this->hasOne(Image::class, 'id', 'cover_image_id');
    }

    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_user');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function flags(): MorphMany
    {
        return $this->morphMany(Flag::class, 'flaggable');
    }

    public function flaggedItems(): HasMany
    {
        return $this->hasMany(Flag::class, 'flagged_by');
    }
}
