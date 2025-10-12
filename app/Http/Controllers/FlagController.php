<?php

namespace App\Http\Controllers;

use App\Models\Flag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlagController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'flaggable_type' => 'required|string|in:App\Models\Post,App\Models\Comment,App\Models\User',
            'flaggable_id' => 'required',
            'reason' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Check if user has already flagged this item
        $existing_flag = Flag::where('flaggable_type', $request->flaggable_type)
            ->where('flaggable_id', $request->flaggable_id)
            ->where('flagged_by', Auth::id())
            ->first();

        if ($existing_flag) {
            return response()->json([
                'success' => false,
                'message' => 'You have already reported this item.'
            ], 422);
        }

        Flag::create([
            'flaggable_type' => $request->flaggable_type,
            'flaggable_id' => $request->flaggable_id,
            'flagged_by' => Auth::id(),
            'reason' => $request->reason,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Item reported successfully.'
        ]);
    }
}
