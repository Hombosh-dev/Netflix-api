<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            return response()->json(['message' => 'Unauthorized. User not authenticated.'], 403);
        }

        if (!$request->user()->hasRole(Role::ADMIN)) {
            return response()->json([
                'message' => 'Unauthorized. Admin access required.',
                'user_role' => $request->user()->role->value ?? 'null',
                'admin_role' => Role::ADMIN->value,
                'is_admin' => $request->user()->isAdmin(),
            ], 403);
        }

        return $next($request);
    }
}
