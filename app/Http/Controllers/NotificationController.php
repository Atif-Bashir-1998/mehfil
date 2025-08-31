<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class NotificationController extends Controller
{
    /**
     * Display a listing of all notifications.
     */
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return Inertia::render('Notification', [
            'notifications' => $user->notifications()->paginate(15),
        ]);
    }

    /**
     * Mark all unread notifications as read.
     */
    public function mark_all_as_read()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $user->unreadNotifications->markAsRead();

        return redirect()->back();
    }

    /**
     * Mark a single notification as read or unread.
     */
    public function mark_as_read(Request $request, DatabaseNotification $notification)
    {
        // Ensure the authenticated user owns this notification
        if ($request->user()->id !== $notification->notifiable_id) {
            abort(403);
        }

        if ($notification->read_at) {
            $notification->markAsUnread();
        } else {
            $notification->markAsRead();
        }

        return redirect()->back();
    }

    /**
     * Delete a single notification.
     */
    public function destroy(Request $request, DatabaseNotification $notification)
    {
        // Ensure the authenticated user owns this notification
        if ($request->user()->id !== $notification->notifiable_id) {
            abort(403);
        }

        $notification->delete();

        return redirect()->back();
    }
}
