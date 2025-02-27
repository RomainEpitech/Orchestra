<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAuthority
{
    public static function parameters(string $module, string $permission): string
    {
        return static::class . ':' . $module . ',' . $permission;
    }

    public function handle(Request $request, Closure $next, string $module, string $permission): Response
    {
        $user = $request->user();
        
        if (!$user || !$user->role) {
            return response()->json([
                'message' => 'User has no role assigned'
            ], 403);
        }

        $authority = $user->role->authority;

        // Admin check
        if (isset($authority['*'])) {
            return $next($request);
        }

        // Module and permission check
        if (!isset($authority[$module]) || !isset($authority[$module][$permission])) {
            return response()->json([
                'message' => "No $permission permission defined for module $module"
            ], 403);
        }

        if (!$authority[$module][$permission]) {
            return response()->json([
                'message' => "Access denied: Requires $module.$permission permission"
            ], 403);
        }

        return $next($request);
    }
}