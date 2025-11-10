<?php

namespace App\Models;

use App\Enums\AdStatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Ad extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        return url(Storage::url($this->image_path));
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Helper methods
    public function isActive(): bool
    {
        return $this->status === AdStatusType::ACTIVE &&
               $this->starts_at <= now() &&
               $this->ends_at >= now();
    }

    public function incrementClicks(): void
    {
        $this->increment('clicks');
    }

    public function incrementImpressions(): void
    {
        $this->increment('impressions');
    }

    public static function getActiveAdsWithTracking()
    {
        return self::where('status', AdStatusType::ACTIVE->value)
            ->inRandomOrder()
            ->take(5)
            ->get()
            ->each(function ($ad) {
                $ad->incrementImpressions();
            });
    }
}
