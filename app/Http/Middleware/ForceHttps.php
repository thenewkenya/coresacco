<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttps
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only force HTTPS in production
        if (app()->environment('production')) {
            // Check if the request is not secure (HTTP)
            if (!$request->secure()) {
                // Get the current URL and convert to HTTPS
                $url = $request->getUri();
                $httpsUrl = str_replace('http://', 'https://', $url);
                return redirect($httpsUrl, 301);
            }
        }

        return $next($request);
    }
}
