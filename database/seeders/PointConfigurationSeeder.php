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
                'action_type' => PointType::CREATED_POST,
                'description' => 'Points awarded for creating a new post',
                'points' => 10,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::CREATED_COMMENT,
                'description' => 'Points awarded for commenting on a post',
                'points' => 2,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::REACTED_POST,
                'description' => 'Points awarded for reacting to a post',
                'points' => 5,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::REACTED_COMMENT,
                'description' => 'Points awarded for reacting to a comment',
                'points' => 1,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::POST_GOT_REACTION,
                'description' => 'Points awarded to poster when post receives a reaction',
                'points' => 3,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::POST_GOT_COMMENT,
                'description' => 'Points awarded to poster when post receives a comment',
                'points' => 7,
                'is_active' => true,
            ],
            [
                'action_type' => PointType::COMMENT_GOT_COMMENT,
                'description' => 'Points awarded to commentor when your comment receives a reply',
                'points' => 5,
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
