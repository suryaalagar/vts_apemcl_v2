<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;

class CookieAttributesMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Set HTTPOnly flag
        $response->headers->setCookie(Cookie::make('apemcl_cookie', 'apemcl_cookie_value', 0, '/', null, false, true));

        // Set Secure flag (only for HTTPS)
        if ($request->isSecure()) {
            $response->headers->setCookie(Cookie::make('apemcl_cookie', 'apemcl_cookie_value', 0, '/', null, true, true));
        }

        // Set SameSite attribute to "Strict"
        $response->headers->setCookie(Cookie::make('apemcl_cookie', 'apemcl_cookie_value', 0, '/', null, false, true, true, 'Strict'));

        // Set Expires attribute to "Session"
        // (Expires not explicitly set, as it defaults to a session cookie)

        // Set Path attribute to a specific path
        $response->headers->setCookie(Cookie::make('apemcl_cookie', 'apemcl_cookie_value', 0, '/app/'));

        return $response;
    }
}
