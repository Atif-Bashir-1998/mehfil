<?php

namespace App\Http\Controllers;

use App\Events\NewMessageSent;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class MessageController extends Controller
{
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        // Load conversations with the last message and other participants
        $conversations = $user->conversations()
            ->with(['participants' => function ($query) use ($user) {
                $query->where('users.id', '!=', $user->id);
            }, 'messages' => function ($query) {
                $query->latest()->limit(1);
            }])
            ->get();

        return Inertia::render('message/Index', [
            'conversations' => $conversations,
        ]);
    }

    public function show(Conversation $conversation)
    {
        /** @var User $user */
        $user = Auth::user();

        // Ensure the authenticated user is a participant
        if (!$conversation->participants()->where('user_id', $user->id)->exists()) {
            abort(403);
        }

        $messages = $conversation->messages()->latest()->paginate(20);

        return response()->json([
            'messages' => $messages,
        ]);
    }

    public function store(Request $request, Conversation $conversation)
    {
        $request->validate(['body' => 'required|string']);

        $message = $conversation->messages()->create([
            'user_id' => $request->user()->id,
            'body' => $request->body,
        ]);

        // Broadcast the new message in real-time
        broadcast(new NewMessageSent($message))->toOthers();

        return back();
    }

    /**
     * Finds or creates a new conversation and sends the first message.
     */
    public function start_conversation(Request $request, User $recipient)
    {
        $request->validate(['body' => 'required|string']);

        $sender = $request->user();

        // Find existing conversation between the two users
        $conversation = Conversation::whereHas('participants', function ($query) use ($sender) {
            $query->where('user_id', $sender->id);
        })->whereHas('participants', function ($query) use ($recipient) {
            $query->where('user_id', $recipient->id);
        })->first();

        // If no conversation exists, create a new one
        if (!$conversation) {
            $conversation = Conversation::create();
            $conversation->participants()->attach([$sender->id, $recipient->id]);
        }

        // Create the first message
        $message = $conversation->messages()->create([
            'user_id' => $sender->id,
            'body' => $request->body,
        ]);

        // Broadcast the new message
        broadcast(new NewMessageSent($message))->toOthers();

        // Redirect to the newly created/found conversation
        return redirect()->route('message.show', $conversation->id);
    }
}
