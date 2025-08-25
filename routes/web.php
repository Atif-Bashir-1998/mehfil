<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ReactionController;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home');

Route::group([
    'middleware' => ['auth', 'verified']
], function () {
    Route::get('dashboard', function () {
        return Inertia::render('Dashboard');
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
