<?php

namespace Database\Seeders;

use App\Enums\ReactionType;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Reaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        Post::factory()
            ->count(50)
            ->sequence(fn() => ['created_by' => $users->random()->id])
            ->create()
            ->each(function ($post) use ($users) {
                // Add random comments (0-10 per post)
                $commentCount = rand(0, 10);
                for ($i = 0; $i < $commentCount; $i++) {
                    $comment = Comment::factory()->create([
                        'post_id' => $post->id,
                        'user_id' => $users->random()->id,
                    ]);

                    // Randomly add replies to some comments (0-3 replies)
                    if (rand(0, 1)) {
                        $replyCount = rand(0, 3);
                        for ($j = 0; $j < $replyCount; $j++) {
                            Comment::factory()->create([
                                'post_id' => $post->id,
                                'user_id' => $users->random()->id,
                                'parent_id' => $comment->id,
                            ]);
                        }
                    }
                }

                // Add random reactions (0-15 per post)
                $reactionCount = rand(0, 15);
                $reactedUsers = $users->random(min($reactionCount, $users->count()));

                foreach ($reactedUsers as $user) {
                    Reaction::create([
                        'id' => Str::uuid(),
                        'post_id' => $post->id,
                        'user_id' => $user->id,
                        'type' => ReactionType::cases()[array_rand(ReactionType::cases())]->value,
                    ]);
                }
            });
    }
}
