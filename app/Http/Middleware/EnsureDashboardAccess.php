<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureDashboardAccess
{
    /**
     * Protect /admin/* routes and optionally check required role types (super_admin, reseller, client).
     */
    public function handle(Request $request, Closure $next, string ...$allowedRoles): Response
    {
        if (!Auth::check()) {
            // In local development, if auto-demo-login is enabled via env, sign in the first super_admin
            if (app()->environment('local') && env('ADMIN_AUTO_LOGIN_LOCAL', true)) {
                $defaultAdmin = \App\Models\User::where('type', 'super_admin')->first()
                    ?? \App\Models\User::first();
                if ($defaultAdmin) {
                    Auth::login($defaultAdmin);
                }
            }
        }

        if (!Auth::check()) {
            return redirect()->guest(route('login'));
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!empty($allowedRoles)) {
            $panel = $user->panelType();
            if (!in_array($panel, $allowedRoles, true) && !$user->isSuperAdmin()) {
                abort(403, 'You do not have permission to access this section.');
            }
        }

        return $next($request);
    }
}
