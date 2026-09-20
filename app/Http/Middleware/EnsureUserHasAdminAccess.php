<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gates the /admin area to users with an admin-facing role. Individual
 * screens (e.g. club-only settings) further narrow access with
 * $request->user()->isSuperAdmin() checks as needed — this middleware
 * just keeps plain members out of the admin shell entirely.
 */
class EnsureUserHasAdminAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! ($user->isSuperAdmin() || $user->isClubAdmin())) {
            abort(403);
        }

        return $next($request);
    }
}
