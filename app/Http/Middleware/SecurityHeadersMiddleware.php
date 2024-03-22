<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


class SecurityHeadersMiddleware
{
    public function handle($request, Closure $next)
    {
        // Add security headers
        $response = $next($request);
        $response->header('Content-Security-Policy', 'self');
        $response->header('X-Frame-Options', 'DENY');
        $response->header('X-XSS-Protection', '1; mode=block');
        $response->header('X-Content-Type-Options', 'nosniff');
        $response->header('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->header('Strict-Transport-Security', 'max-age=63072000; includeSubdomains');

        return $response;
    }
}
