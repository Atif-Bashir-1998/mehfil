<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\FlagController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReactionController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::get('/privacy-policy', function () {
    return Inertia::render('PrivacyPolicy');
})->name('privacy-policy');

Route::get('/community-guidelines', function () {
    return Inertia::render('CommunityGuidelines');
})->name('community-guidelines');

Route::group([
    'middleware' => ['auth', 'verified']
], function () {
    Route::get('dashboard', function () {
        // return Inertia::render('Dashboard');
        return redirect(route('post.index'));
    })->middleware(['auth', 'verified'])->name('dashboard');

    Route::resource('post', PostController::class);
    Route::get('/profile/{user}', function (User $user) {
        return Inertia::render('user/Show', [
            'user' => $user->load([
                'posts' => function ($query) {
                    $query->orderBy('created_at', 'desc')
                        ->withCount(['reactions', 'all_comments']);
                },
                'posts.creator',
                'posts.comments'
            ])
        ]);
    })->name('user.show');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'mark_all_as_read'])->name('notification.mark-all-as-read');
    Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'mark_as_read'])->name('notification.mark-as-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notification.destroy');

    Route::get('/message', [MessageController::class, 'index'])->name('message.index');
    Route::get('/message/{conversation}', [MessageController::class, 'show'])->name('message.show');
    Route::post('/message/{conversation}', [MessageController::class, 'store'])->name('message.store');
    Route::post('/message/start/{recipient}', [MessageController::class, 'start_conversation'])->name('message.start-conversation');


    // Reactions routes
    Route::post('/post/{post}/reactions', [ReactionController::class, 'store'])->name('post.reactions.store');
    Route::delete('/post/{post}/reactions', [ReactionController::class, 'destroy'])->name('post.reactions.destroy');

    // Comments routes
    Route::post('/post/{post}/comment', [CommentController::class, 'store'])->name('post.comment.store');
    Route::put('/comment/{comment}', [CommentController::class, 'update'])->name('comment.update');
    Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');

    Route::post('/flag', [FlagController::class, 'store'])->name('flag.store');
    Route::resource('ad', AdController::class)->except(['show']);
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/api.php';
