<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OptimizeQueries
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Enable query logging in debug mode
        if (config('app.debug')) {
            DB::enableQueryLog();
        }

        $response = $next($request);

        // Log slow queries in production
        if (!config('app.debug') && DB::getQueryLog()) {
            $slowQueries = collect(DB::getQueryLog())
                ->filter(fn($query) => $query['time'] > 1000) // Queries taking more than 1 second
                ->toArray();
            
            if (!empty($slowQueries)) {
                \Log::warning('Slow queries detected', [
                    'queries' => $slowQueries,
                    'url' => $request->url(),
                    'method' => $request->method()
                ]);
            }
        }

        return $response;
    }
}
