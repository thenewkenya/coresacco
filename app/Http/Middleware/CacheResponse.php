<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, $ttl = 300)
    {
        // Only cache GET requests for authenticated users
        if ($request->isMethod('GET') && auth()->check()) {
            $cacheKey = 'response_' . md5($request->fullUrl() . auth()->id());
            
            // Check if response is cached
            if (Cache::has($cacheKey)) {
                return response(Cache::get($cacheKey))
                    ->header('X-Cache', 'HIT');
            }
            
            $response = $next($request);
            
            // Cache successful responses
            if ($response->getStatusCode() === 200) {
                Cache::put($cacheKey, $response->getContent(), $ttl);
                $response->header('X-Cache', 'MISS');
            }
            
            return $response;
        }
        
        return $next($request);
    }
}
