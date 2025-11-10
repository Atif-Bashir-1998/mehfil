<?php

use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Models\Ad;

Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('posts', [PostController::class, 'get_posts'])->name('api.posts.get');
    Route::get('/ads/{ad}/click', function (Ad $ad) {
        $ad->incrementClicks();

        // Redirect to the target URL
        return back();
    })->name('api.ad.click');
});
