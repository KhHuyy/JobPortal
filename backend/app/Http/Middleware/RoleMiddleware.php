<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request and verify the authenticated user's role.
     *
     * Usage in routes:  ->middleware('role:admin')
     *                   ->middleware('role:recruiter')
     *                   ->middleware('role:candidate')
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated. Please log in.',
                'data'    => null,
                'errors'  => null,
            ], 401);
        }

        if ($user->status === 'banned') {
            return response()->json([
                'success' => false,
                'message' => 'Your account has been banned. Please contact support.',
                'data'    => null,
                'errors'  => null,
            ], 403);
        }

        if (! in_array($user->role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Forbidden. You do not have permission to access this resource.',
                'data'    => null,
                'errors'  => null,
            ], 403);
        }

        return $next($request);
    }
}

