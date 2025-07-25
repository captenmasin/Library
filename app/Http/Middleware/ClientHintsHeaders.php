<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClientHintsHeaders
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (! config('services.pirsch.enabled')) {
            return $response;
        }

        // Keep all in Accept-CH
        $response->headers->set(
            'Accept-CH',
            'Sec-CH-UA, Sec-CH-UA-Mobile, Sec-CH-UA-Platform, Sec-CH-UA-Platform-Version, Sec-CH-Width, Sec-CH-Viewport-Width, Width, Viewport-Width'
        );

        // Only valid features in Permissions-Policy
        $response->headers->set(
            'Permissions-Policy',
            'ch-ua=(self "https://api.pirsch.io"), ch-ua-mobile=(self "https://api.pirsch.io"), ch-ua-platform=(self "https://api.pirsch.io"), ch-ua-platform-version=(self "https://api.pirsch.io"), ch-width=(self "https://api.pirsch.io"), ch-viewport-width=(self "https://api.pirsch.io")'
        );

        return $response;
    }
}
