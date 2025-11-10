<?php

namespace App\Services;

use App\Models\BoostedPost;
use App\Models\Post;

class BoostService
{
    const BOOST_COST = 100; // Points per day
    const BOOST_DURATION = 24; // Hours

    public static function boostPost(Post $post, int $days = 1)
    {
        $user = $post->creator;
        $pointsNeeded = self::BOOST_COST * $days;

        PointsService::spendPoints(
            $user,
            'post_boosted',
            $pointsNeeded,
            $post,
            "Boosted post for {$days} days"
        );

        $boostedPost = BoostedPost::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'points_spent' => $pointsNeeded,
            'boosted_until' => now()->addHours(self::BOOST_DURATION * $days),
        ]);

        return $boostedPost;
    }

    public static function getActiveBoostedPosts()
    {
        return BoostedPost::active()
            ->with('post.creator', 'post.images')
            ->get()
            ->pluck('post');
    }
}
