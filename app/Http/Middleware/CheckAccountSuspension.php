<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckAccountSuspension
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Update last login timestamp
            $user->updateLastLogin();
            
            // Check if account is suspended
            if ($user->is_suspended) {
                // If scheduled for deletion and past due, redirect to deletion notice
                if ($user->shouldBeDeleted()) {
                    Auth::logout();
                    return redirect()->route('login')
                        ->with('error', 'Your account has been permanently deleted due to inactivity.');
                }
                
                // If suspended but not yet deleted, show suspension notice
                return redirect()->route('account.suspended')
                    ->with('error', 'Your account has been suspended. Please contact support for assistance.');
            }
        }

        return $next($request);
    }
}