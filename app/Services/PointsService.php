<?php

namespace App\Services;

use App\Models\PointConfiguration;
use App\Models\PointTransaction;
use App\Models\User;

class PointsService
{
    public static function awardPoints(User $user, string $actionType, $transactionable = null, string $description = 'No description')
    {
        $points = PointConfiguration::get_points_for_action($actionType);

        if ($points > 0) {
            $user->increment('points', $points);

            PointTransaction::create([
                'user_id' => $user->id,
                'type' => 'earned',
                'points' => $points,
                'action_type' => $actionType,
                'description' => $description,
                'transactionable_type' => $transactionable ? get_class($transactionable) : null,
                'transactionable_id' => $transactionable?->id,
            ]);

            return $points;
        }

        return 0;
    }

    public static function spendPoints(User $user, string $actionType, int $points, $transactionable = null, string $description = 'No description')
    {
        if ($user->points < $points) {
            throw new \Exception('Insufficient points');
        }

        $user->decrement('points', $points);

        PointTransaction::create([
            'user_id' => $user->id,
            'type' => 'spent',
            'points' => $points,
            'action_type' => $actionType,
            'description' => $description,
            'transactionable_type' => $transactionable ? get_class($transactionable) : null,
            'transactionable_id' => $transactionable?->id,
        ]);

        return true;
    }
}
