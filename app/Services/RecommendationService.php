<?php
// app/Services/RecommendationService.php

namespace App\Services;

use App\Models\Post;
use App\Models\User;
use App\Models\InterestTag;
use Illuminate\Support\Facades\DB;

class RecommendationService
{
    private const LIKE_WEIGHT = 1.5;
    private const COMMENT_WEIGHT = 1.2;
    private const VIEW_WEIGHT = 0.3;
    private const DAMPING_FACTOR = 0.9; // Reduces impact of older interactions

    /**
     * Update interest tags from post creation
     */
    public function updateTagsFromPost(array $tags): void
    {
        foreach ($tags as $tagName) {
            $normalizedTag = strtolower(trim($tagName));

            InterestTag::updateOrCreate(
                ['name' => $normalizedTag],
                ['usage_count' => DB::raw('usage_count + 1')]
            );
        }
    }

    /**
     * Update user interests based on interaction
     */
    public function updateUserInterests(User $user, Post $post, string $interactionType): void
    {
        $tags = $post->tags ?? [];
        $weight = $this->getWeightForInteraction($interactionType);

        foreach ($tags as $tagName) {
            $normalizedTag = strtolower(trim($tagName));

            // Create tag if it doesn't exist
            $tag = InterestTag::firstOrCreate(
                ['name' => $normalizedTag],
                ['usage_count' => 0]
            );

            // Increment usage count
            $tag->increment('usage_count');

            // Update user interest score
            $this->updateUserInterestScore($user, $tag, $weight);
        }
    }

    /**
     * Get weight for different interaction types
     */
    private function getWeightForInteraction(string $interactionType): float
    {
        return match($interactionType) {
            'like' => self::LIKE_WEIGHT,
            'comment' => self::COMMENT_WEIGHT,
            'view' => self::VIEW_WEIGHT,
            default => 0.5
        };
    }

    /**
     * Update user's interest score with logarithmic damping
     */
    private function updateUserInterestScore(User $user, InterestTag $tag, float $weight): void
    {
        $currentScore = $user->getInterestScore($tag->name);

        // Logarithmic growth with damping: new_score = (current_score + weight) * damping
        $newScore = ($currentScore + $weight) * self::DAMPING_FACTOR;

        // Ensure score doesn't become too small
        $newScore = max($newScore, 0.1);

        $user->interests()->syncWithoutDetaching([
            $tag->id => ['score' => $newScore]
        ]);
    }

    /**
     * Get recommended posts for user
     */
    public function getRecommendedPosts(User $user, int $limit = 10): array
    {
        // Get user's top interests
        $topInterests = $user->interests()
            ->orderByDesc('score')
            ->limit(5)
            ->pluck('name')
            ->toArray();

        if (empty($topInterests)) {
            // Fallback to popular posts if no interests
            return $this->getFallbackPosts($limit);
        }

        // Get posts matching user's interests with scoring
        $posts = Post::where(function ($query) use ($topInterests) {
                foreach ($topInterests as $interest) {
                    $query->orWhereJsonContains('tags', $interest);
                }
            })
            ->where('is_hidden', false)
            ->with('creator')
            ->get();

        // Score and shuffle posts
        return $this->scoreAndShufflePosts($posts, $user, $topInterests, $limit);
    }

    /**
     * Score posts based on user interests and shuffle
     */
    private function scoreAndShufflePosts($posts, User $user, array $topInterests, int $limit): array
    {
        $scoredPosts = [];

        foreach ($posts as $post) {
            $score = $this->calculatePostScore($post, $user, $topInterests);
            $scoredPosts[] = [
                'post' => $post,
                'score' => $score
            ];
        }

        // Sort by score descending
        usort($scoredPosts, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });

        // Take top posts and shuffle some for variety
        $topPosts = array_slice($scoredPosts, 0, $limit * 2);
        $shuffled = array_slice($topPosts, 0, $limit);

        // Add some random lower-scored posts for discovery
        if (count($scoredPosts) > $limit) {
            $randomPosts = array_slice($scoredPosts, $limit, $limit);
            shuffle($randomPosts);
            $randomSelection = array_slice($randomPosts, 0, min(2, count($randomPosts)));

            foreach ($randomSelection as $randomPost) {
                $shuffled[] = $randomPost;
            }
        }

        // Final shuffle and limit
        shuffle($shuffled);
        return array_slice($shuffled, 0, $limit);
    }

    /**
     * Calculate score for a post based on user interests
     */
    private function calculatePostScore(Post $post, User $user, array $topInterests): float
    {
        $score = 0;
        $postTags = $post->tags ?? [];

        foreach ($postTags as $tag) {
            $normalizedTag = strtolower(trim($tag));
            $userScore = $user->getInterestScore($normalizedTag);

            // Boost score if tag is in top interests
            if (in_array($normalizedTag, $topInterests)) {
                $userScore *= 1.5;
            }

            $score += $userScore;
        }

        return $score;
    }

    /**
     * Fallback to popular posts when no user interests
     */
    private function getFallbackPosts(int $limit): array
    {
        return Post::withCount(['reactions', 'comments'])
            ->where('is_hidden', false)
            ->with('creator')
            ->orderByDesc('reactions_count')
            ->orderByDesc('comments_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Get recommended posts for user with pagination
     */
    public function getRecommendedPostsPaginated(User $user, int $perPage = 15, int $page = 1)
    {
        // Get user's top interests
        $topInterests = $user->interests()
            ->orderByDesc('score')
            ->limit(5)
            ->pluck('name')
            ->toArray();

        if (empty($topInterests)) {
            // Fallback to popular posts if no interests
            return $this->getFallbackPostsPaginated($perPage, $page);
        }

        // Build base query for posts matching user's interests
        $query = Post::where(function ($query) use ($topInterests) {
                foreach ($topInterests as $interest) {
                    $query->orWhereJsonContains('tags', $interest);
                }
            })
            ->where('is_hidden', false)
            ->with(['creator:id,name', 'reactions', 'comments', 'all_comments', 'images'])
            ->withCount(['reactions', 'all_comments'])
            ->with(['reactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->whereHas('creator', function ($query) {
                $query->where('is_hidden', false);
            });

        // Get total count
        $total = $query->count();

        // Apply pagination
        $posts = $query->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();

        // Score and shuffle the paginated results for variety
        $scoredPosts = $this->scoreAndShufflePosts($posts, $user, $topInterests, $perPage);

        return [
            'data' => $scoredPosts,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }

    /**
     * Fallback to popular posts with pagination
     */
    private function getFallbackPostsPaginated(int $perPage, int $page)
    {
        $query = Post::withCount(['reactions', 'all_comments'])
            ->where('is_hidden', false)
            ->with(['creator:id,name', 'reactions', 'comments', 'all_comments', 'images'])
            ->whereHas('creator', function ($query) {
                $query->where('is_hidden', false);
            })
            ->orderByDesc('reactions_count')
            ->orderByDesc('comments_count');

        $total = $query->count();
        $posts = $query->skip(($page - 1) * $perPage)
                    ->take($perPage)
                    ->get();

        return [
            'data' => $posts,
            'total' => $total,
            'per_page' => $perPage,
            'current_page' => $page,
            'last_page' => ceil($total / $perPage)
        ];
    }
}
