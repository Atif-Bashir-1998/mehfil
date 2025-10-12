<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $appends = ['image_url'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    // Accessor for full image URL
    public function getImageUrlAttribute()
    {
        return url(Storage::url($this->path));
    }
}
