<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (!$request->user()) {
            abort(403, 'Unauthorized action.');
        }

        // ✅ If only one argument is passed, it might be comma-separated
        if (count($permissions) === 1 && str_contains($permissions[0], ',')) {
            $permissions = array_map('trim', explode(',', $permissions[0]));
        }

        // ✅ Check if user has ANY of the permissions (OR logic)
        foreach ($permissions as $permission) {
            if ($request->user()->hasPermission($permission)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized action.');
    }
}
