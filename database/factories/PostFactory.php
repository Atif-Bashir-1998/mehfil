<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'created_by' => User::factory(),
            'title' => fake()->sentence(),
            'content' => fake()->paragraphs(3, true),
            'tags' => fake()->randomElements([
                'technology', 'sports', 'politics', 'entertainment',
                'education', 'health', 'travel', 'food', 'business',
                'cricket', 'football', 'movies', 'music', 'culture',
                'karachi', 'lahore', 'islamabad', 'pakistan', 'urdu'
            ], fake()->numberBetween(0, 5)),
        ];
    }
}
