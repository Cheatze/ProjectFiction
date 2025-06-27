<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;


class EnsureUserOwnsProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $authenticatedUserId = Auth::id();
        $uriUserId = $request->route('user')->id;

        // Check if the authenticated user's ID matches the URI user ID
        if ($authenticatedUserId && (int) $authenticatedUserId === (int) $uriUserId) {
            return $next($request); // User is authorized, proceed with the request
        }

        // If not authorized abort
        abort(403, 'Unauthorized action. You can only view your own private profile.');

    }
}
