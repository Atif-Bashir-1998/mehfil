<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Post;
use App\Rules\HateSpeech;
use App\Rules\ImageHateSpeech;
use App\Utils\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $posts = Post::with(['creator:id,name', 'reactions', 'comments', 'all_comments', 'images'])
            ->withCount(['reactions', 'all_comments'])
            ->with(['reactions' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
            // exclude posts where 'is_hidden' is true
            ->where('is_hidden', false)
            // exclude posts that the current user has flagged with a 'pending' status
            ->whereDoesntHave('flags', function ($query) use ($user) {
                $query->where('flagged_by', $user->id) // flagged by the current user
                    ->where('status', 'pending');     // status is pending
            })

            ->latest()
            ->paginate(15);

        return Inertia::render('post/Index', [
            'posts' => $posts,
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
        ]);

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
}
