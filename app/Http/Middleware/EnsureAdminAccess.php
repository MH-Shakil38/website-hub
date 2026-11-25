<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards admin routes. Use after the `auth` middleware:
 *   ->middleware(['auth', 'admin'])                 active account only
 *   ->middleware(['auth', 'admin:manage_websites']) active account + permission slug
 * Administrators always pass the permission check (see User::hasPermission()).
 */
class EnsureAdminAccess
{
    public function handle(Request $request, Closure $next, ?string $permission = null): Response
    {
        $user = $request->user();

        if (!$user || ($user->status !== null && $user->status !== 'Active')) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Your account is inactive.']);
        }

        if ($permission !== null && !$user->hasPermission($permission)) {
            abort(403, 'You do not have permission to access this section.');
        }

        return $next($request);
    }
}
