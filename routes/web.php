<?php

use App\Http\Controllers\CommentController;
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

    Route::middleware(['auth'])->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notification.index');
        Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'mark_all_as_read'])->name('notification.mark-all-as-read');
        Route::post('/notifications/{notification}/mark-as-read', [NotificationController::class, 'mark_as_read'])->name('notification.mark-as-read');
        Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notification.destroy');
    });

    // Reactions routes
    Route::post('/post/{post}/reactions', [ReactionController::class, 'store'])->name('post.reactions.store');
    Route::delete('/post/{post}/reactions', [ReactionController::class, 'destroy'])->name('post.reactions.destroy');

    // Comments routes
    Route::post('/post/{post}/comment', [CommentController::class, 'store'])->name('post.comment.store');
    Route::put('/comment/{comment}', [CommentController::class, 'update'])->name('comment.update');
    Route::delete('/comment/{comment}', [CommentController::class, 'destroy'])->name('comment.destroy');
});

require __DIR__ . '/settings.php';
require __DIR__ . '/auth.php';
