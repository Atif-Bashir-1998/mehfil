<?php

namespace App\Http\Controllers;

use App\Enums\ReactionType;
use App\Models\Post;
use App\Models\Reaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReactionController extends Controller
{
    /**
     * Store or update a reaction to a post.
     */
    public function store(Request $request, Post $post)
    {
        $request->validate([
            'type' => ['required', 'in:' . implode(',', ReactionType::values())]
        ]);

        $user = Auth::user();

        Reaction::updateOrCreate(
            [
                'post_id' => $post->id,
                'user_id' => $user->id
            ],
            [
                'type' => $request->type
            ]
        );

        return redirect()->back()->with('success', 'Reaction added successfully!');
    }

    /**
     * Remove a reaction from a post.
     */
    public function destroy(Post $post)
    {
        $user = Auth::user();

        $reaction = Reaction::where('post_id', $post->id)
            ->where('user_id', $user->id)
            ->first();

        if ($reaction) {
            $reaction->delete();
            return redirect()->back()->with('success', 'Reaction removed successfully!');
        }

        return redirect()->back()->with('error', 'Reaction not found!');
    }
}
