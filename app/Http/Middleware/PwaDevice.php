<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Symfony\Component\HttpFoundation\Response;

class PwaDevice
{
    const ONE_YEAR_MINUTES = 60 * 24 * 365;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->boolean('pwa-mode')) {
            Cookie::queue('pwa-mode', 'true', self::ONE_YEAR_MINUTES);

            if ($request->boolean('ios')) {
                Cookie::queue('pwa-device', 'ios', self::ONE_YEAR_MINUTES);
            } elseif ($request->boolean('android')) {
                Cookie::queue('pwa-device', 'android', self::ONE_YEAR_MINUTES);
            }
        }

        if (! $request->boolean('pwa-mode')) {
            Cookie::queue(Cookie::forget('pwa-mode'));
            Cookie::queue(Cookie::forget('pwa-device'));
        }

        return $response;
    }
}
