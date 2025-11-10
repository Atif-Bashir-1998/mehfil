<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Post;
use App\Rules\HateSpeech;
use App\Rules\ImageHateSpeech;
use App\Services\RecommendationService;
use App\Utils\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    private RecommendationService $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('post/Index', [
            'reaction_types' => ReactionType::all()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('post/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', new HateSpeech],
            'content' => ['required', 'string', new HateSpeech],
            'tags' => 'nullable|array',
            'tags.*' => ['string', 'max:50', new HateSpeech],
            'images' => 'nullable|array|max:10',
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', new ImageHateSpeech],
        ]);

        if (!empty($validated['tags'])) {
            $this->recommendationService->updateTagsFromPost($validated['tags']);
        }

        $post = $request->user()->posts()->create($validated);

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $image) {
                $webp_path = ImageHelper::convert_to_webp($image);

                $post->images()->create([
                    'path' => $webp_path,
                    'order' => $post->images()->count() + $index
                ]);
            }
        }

        return redirect()->route('dashboard')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load([
            'creator:id,name',
            'images',
            'comments' => function ($query) {
                $query->whereNull('parent_id')
                    ->with(['user', 'replies.user', 'image']) // This will load all nested replies recursively
                    ->orderBy('created_at', 'desc');
            }
        ])->loadCount(['reactions', 'all_comments']);

        return Inertia::render('post/Show', [
            'post' => $post
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        Gate::authorize('update', $post);

        return Inertia::render('post/Create', [
            'post' => $post->load('images')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        // First, validate the incoming request data
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', new HateSpeech],
            'content' => ['required', 'string', new HateSpeech],
            'tags' => 'nullable|array',
            'tags.*' => ['string', 'max:50', new HateSpeech],
            // Rule for new images being uploaded
            'images' => 'nullable|array|max:10',
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048', new ImageHateSpeech],
            // Rule for images to be deleted
            'deleted_image_ids' => 'nullable|array',
            'deleted_image_ids.*' => 'uuid|exists:images,id',
        ]);

        Gate::authorize('update', $post);

        // Handle the deletion of images first
        if ($request->has('deleted_image_ids')) {
            $images_to_delete = $post->images()->whereIn('id', $validated['deleted_image_ids'])->get();
            foreach ($images_to_delete as $image) {
                // Delete the file from storage
                Storage::delete($image->path);
                // Delete the record from the database
                $image->delete();
            }
        }

        // Handle the addition of new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $webp_path = ImageHelper::convert_to_webp($imageFile);
                // Create the new image record, associating it with the post
                $post->images()->create([
                    'path' => $webp_path, // Note: your schema uses `path`, but your code used `image_path`
                    'order' => $post->images()->count(),
                ]);
            }
        }

        // Finally, update the post's main attributes
        $post->update($validated);

        return redirect()->route('post.edit', ['post' => $post])
            ->with('success', 'Post updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Post deleted successfully!');
    }

    public function get_posts(Request $request)
    {
        $user = Auth::user();

        $posts = Post::with(['creator:id,name', 'reactions', 'comments', 'all_comments', 'images'])
            ->withCount(['reactions', 'all_comments'])
            ->with(['reactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            ->where('is_hidden', false)
            ->whereHas('creator', function ($query) {
                $query->where('is_hidden', false);
            })
            ->whereDoesntHave('flags', function ($query) use ($user) {
                $query->where('flagged_by', $user->id)
                    ->where('status', 'pending');
            })
            ->latest()
            ->paginate(15);

        return response()->json($posts);
    }

    /**
     * Track user interaction with post
     */
    public function trackInteraction(Post $post, Request $request)
    {
        $interactionType = $request->input('type'); // 'like', 'comment', 'view'
        $user = $request->user();

        $this->recommendationService->updateUserInterests($user, $post, $interactionType);

        return response()->json(['success' => true]);
    }

    public function getRecommendedPosts(Request $request)
{
    $user = $request->user();
    $perPage = $request->input('per_page', 15);
    $page = $request->input('page', 1);

    $result = $this->recommendationService->getRecommendedPostsPaginated($user, $perPage, $page);

    return response()->json([
        'data' => $result['data'],
        'current_page' => $result['current_page'],
        'per_page' => $result['per_page'],
        'from' => (($result['current_page'] - 1) * $result['per_page']) + 1,
        'to' => min($result['current_page'] * $result['per_page'], $result['total']),
        'total' => $result['total'],
        'last_page' => $result['last_page'],
        'path' => $request->url(),
        'first_page_url' => $request->url() . '?page=1',
        'last_page_url' => $request->url() . '?page=' . $result['last_page'],
        'next_page_url' => $result['current_page'] < $result['last_page'] ? $request->url() . '?page=' . ($result['current_page'] + 1) : null,
        'prev_page_url' => $result['current_page'] > 1 ? $request->url() . '?page=' . ($result['current_page'] - 1) : null,
    ]);
}

    // /**
    //  * Get recommended posts for user
    //  */
    // public function getRecommendedPosts(Request $request)
    // {
    //     $user = $request->user();
    //     $perPage = $request->input('per_page', 15);
    //     $page = $request->input('page', 1);

    //     // Get all recommended posts (you might want to optimize this)
    //     $allRecommendedPosts = $this->recommendationService->getRecommendedPosts($user, 100); // Get more posts for pagination

    //     // Manual pagination
    //     $total = count($allRecommendedPosts);
    //     $currentPage = $page;
    //     $perPage = $perPage;

    //     $offset = ($currentPage - 1) * $perPage;
    //     $paginatedPosts = array_slice($allRecommendedPosts, $offset, $perPage);

    //     // Create paginator response similar to Laravel's paginate()
    //     return response()->json([
    //         'data' => $paginatedPosts,
    //         'current_page' => $currentPage,
    //         'per_page' => $perPage,
    //         'from' => $offset + 1,
    //         'to' => $offset + count($paginatedPosts),
    //         'total' => $total,
    //         'last_page' => ceil($total / $perPage),
    //         'path' => $request->url(),
    //         'first_page_url' => $request->url() . '?page=1',
    //         'last_page_url' => $request->url() . '?page=' . ceil($total / $perPage),
    //         'next_page_url' => $currentPage < ceil($total / $perPage) ? $request->url() . '?page=' . ($currentPage + 1) : null,
    //         'prev_page_url' => $currentPage > 1 ? $request->url() . '?page=' . ($currentPage - 1) : null,
    //     ]);
    // }
}
