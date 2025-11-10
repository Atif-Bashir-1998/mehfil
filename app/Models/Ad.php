<?php

namespace App\Models;

use App\Enums\AdStatusType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ad extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

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
}
