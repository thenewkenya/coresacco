<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user() || !$request->user()->hasAnyRole($roles)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to access this area.');
        }

        return $next($request);
    }
} 