<?php

use App\Http\Controllers\PostController;
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
    Route::get('/profile/{user}', function(User $user) {
        return Inertia::render('user/Show', [
            'user' => $user->load(['posts' => function($query) {
                $query->orderBy('created_at', 'desc');
            },
            'posts.creator'])
        ]);
    })->name('user.show');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
