<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        if (!$request->user() || !$request->user()->hasAnyPermission($permissions)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized access'
                ], 403);
            }
            
            return redirect()->route('dashboard')
                ->with('error', 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
} 