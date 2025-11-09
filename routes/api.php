<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('posts', [PostController::class, 'get_posts'])->name('api.posts.get');
});
