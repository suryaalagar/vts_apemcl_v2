<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ClickjackingProtectionMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Use X-Frame-Options header
        $response->header('X-Frame-Options', 'DENY');

        // Use Content-Security-Policy header
        $response->header('Content-Security-Policy', 'frame-ancestors \'self\'');

        return $response;
    }
}
