<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LinkedInOAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ensure session is started
        if (!session()->isStarted()) {
            session()->start();
        }
        
        // Disable CSRF protection for LinkedIn OAuth routes
        if ($request->is('api/verification/*/linkedin') || $request->is('linkedin/callback')) {
            return $next($request);
        }
        
        return $next($request);
    }
} 
