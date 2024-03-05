<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CacheControlMiddleware
{
    public function handle($request, Closure $next)
    {
        $response = $next($request);

        // Set cache control headers
        $response->header('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->header('Expires', '0');
        $response->header('Pragma', 'no-cache');

        return $response;
    }
}
