<?php

namespace App\Http\Middleware;

use App\Models\Website;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleIsActive
{
    public function handle(Request $request, Closure $next, string $moduleName): Response
    {
        if (!Website::isModuleActive($moduleName)) {
            if ($request->is('admin/*')) {
                return redirect()->route('admin.websites')
                    ->withErrors(['module' => "The [{$moduleName}] module is currently turned OFF. Enable it from Manage Websites to access its admin panel."]);
            }

            abort(404, "Module [{$moduleName}] is not active.");
        }

        return $next($request);
    }
}
