<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointConfiguration extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public static function get_points_for_action($action_type)
    {
        return static::where('action_type', $action_type)
            ->where('is_active', true)
            ->value('points') ?? 0;
    }
}
