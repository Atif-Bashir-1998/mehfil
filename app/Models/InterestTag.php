<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class InterestTag extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_interests')
            ->withPivot('score')
            ->withTimestamps();
    }
}
