<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();

        $posts = Post::with(['creator:id,name', 'reactions', 'comments', 'all_comments'])
            ->withCount(['reactions', 'all_comments'])
            ->with(['reactions' => function($query) use ($user) {
                $query->where('user_id', $user->id);
            }])
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
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        $request->user()->posts()->create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Post created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        $post->load(['creator:id,name',
            'comments' => function($query) {
                $query->whereNull('parent_id')
                    ->with(['user', 'replies.user']) // This will load all nested replies recursively
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
            'post' => $post
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        Gate::authorize('update', $post);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

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
