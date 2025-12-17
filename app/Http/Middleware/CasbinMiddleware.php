<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Lauthz\Facades\Enforcer;

class CasbinMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $resource = null, $action = null): Response
    {
        if (!auth()->check()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user = auth()->user();
        $role = $user->role ?? 'guest';

        // Default resource and action from route
        if (!$resource) {
            $resource = $request->route()->getName() ?? $request->path();
        }
        if (!$action) {
            $action = $request->method();
        }

        $action = strtoupper($action);

        if (!Enforcer::enforce($role, $resource, $action)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access'
            ], 403);
        }

        return $next($request);
    }
}
