<?php

namespace Database\Seeders;

use App\Enums\PointType;
use App\Models\PointConfiguration;
use Illuminate\Database\Seeder;

class PointConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configurations = [
            [
                'action_type' => PointType::POST_CREATED,
                'description' => 'Points awarded for creating a new post',
                'points' => 10,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::REACTED,
                'description' => 'Points awarded for reacting to a post',
                'points' => 2,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::COMMENT_CREATED,
                'description' => 'Points awarded for commenting on a post',
                'points' => 5,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::COMMENT_REACTED,
                'description' => 'Points awarded for reacting to a comment',
                'points' => 1,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::POST_COMMENTED,
                'description' => 'Points awarded when your post receives a comment',
                'points' => 3,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::POST_REACTED,
                'description' => 'Points awarded when your post receives a reaction',
                'points' => 1,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::COMMENT_REPLIED,
                'description' => 'Points awarded when your comment receives a reply',
                'points' => 2,
                'is_active' => true,
            ],
        ];

        foreach ($configurations as $config) {
            PointConfiguration::updateOrCreate(
                ['action_type' => $config['action_type']],
                $config
            );
        }
    }
}
