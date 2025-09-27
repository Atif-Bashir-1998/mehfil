<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Notifications\CommentReplied;
use App\Notifications\NewComment;
use App\Rules\HateSpeech;
use App\Utils\ImageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    /**
     * Store a newly created comment.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'content' => ['required', 'string', 'max:1000', new HateSpeech],
            'parent_id' => ['nullable', 'exists:comments,id'],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $user = Auth::user();

        $comment = $post->comments()->create([
            'user_id' => $user->id,
            'content' => $request->content,
            'parent_id' => $request->parent_id
        ]);

        // Handle single image
        if ($request->hasFile('image')) {
            $webp_path = ImageHelper::convert_to_webp($request->file('image'));

            $comment->image()->create([
                'path' => $webp_path
            ]);
        }

        // if new comment is a reply to a comment, notify the user to whom it is replied
        if ($request->parent_id) {
            $original_comment = Comment::find($request->input('parent_id'));
            $original_comment->user->notify(new CommentReplied($post, $original_comment, $request->user()));
        }

        $post->creator->notify(new NewComment($post, $request->user()));

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    /**
     * Update the specified comment.
     */
    public function update(Request $request, Comment $comment)
    {
        Gate::authorize('update', $comment);

        $validated = $request->validate([
            'content' => ['required', 'string', 'max:1000', new HateSpeech],
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'remove_image' => 'nullable|boolean' // Flag to remove existing image
        ]);
        // Update comment
        $comment->update(['content' => $validated['content']]);

        // Handle image removal
        if ($validated['remove_image'] ?? false && $comment->image) {
            Storage::disk('public')->delete($comment->image->path);
            $comment->image->delete();
        }

        // Handle new image (replaces existing one)
        if ($request->hasFile('image')) {
            // Delete existing image if any
            if ($comment->image) {
                Storage::disk('public')->delete($comment->image->path);
                $comment->image->delete();
            }

            $image = $request->file('image');
            $webp_path = ImageHelper::convert_to_webp($image);

            $comment->image()->create([
                'path' => $webp_path,
            ]);
        }

        return redirect()->back()->with('success', 'Comment updated successfully!');
    }

    /**
     * Remove the specified comment.
     */
    public function destroy(Comment $comment)
    {
        Gate::authorize('delete', $comment);

        $comment->delete();

        return redirect()->back()->with('success', 'Comment deleted successfully!');
    }
}
