<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Flag extends Model
{
    use HasUuids;

    protected $fillable = [
        'flaggable_id',
        'flaggable_type',
        'flagged_by',
        'reason',
        'status',
        'admin_notes',
        'resolved_at',
        'resolved_by'
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public function flaggable(): MorphTo
    {
        return $this->morphTo();
    }

    public function flaggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function resolvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    // Scope for pending flags
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for resolved flags
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }
}
