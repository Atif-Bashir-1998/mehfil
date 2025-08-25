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
            'user' => $user->with('posts')
        ]);
    });
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
