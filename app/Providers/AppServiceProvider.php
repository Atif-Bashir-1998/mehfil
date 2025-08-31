<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Inertia::share([
            'notifications' => function () {
                if (Auth::check()) {
                    /** @var User $user */
                    $user = Auth::user();

                    return [
                        'unread_count' => $user->unreadNotifications()->count(),
                        'latest' => $user->notifications()->take(5)->get(),
                    ];
                }

                return null;
            },
        ]);
    }
}
