<?php

namespace App\Http\Middleware;

use App\Utils\RoleHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var User user */
        $user = Auth::user();

        $allowed_role = $user->hasRole([
            RoleHelper::DEFAULT_ROLES['ADMIN'],
            RoleHelper::DEFAULT_ROLES['DEVELOPER'],
            RoleHelper::DEFAULT_ROLES['MODERATOR']
        ]);

        if ($allowed_role) {
            return $next($request);
        }

        abort(403, __('Unauthorized action.'));
    }
}
