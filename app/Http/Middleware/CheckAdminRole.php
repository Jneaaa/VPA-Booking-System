<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\AdminRoles;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role_id, [AdminRoles::HEAD_ADMIN, AdminRoles::INVENTORY_MANAGER])) {
            \Log::warning('Unauthorized access attempt', [
                'user_id' => $user->id ?? null,
                'ip' => $request->ip(),
            ]);
            return response()->json(['message' => 'Forbidden: You are not authorized.'], 403);
        }

        return $next($request);
    }
}
