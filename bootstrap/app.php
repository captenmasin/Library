<?php

use App\Http\Middleware\StripCookies;
use Illuminate\Foundation\Application;
use App\Http\Middleware\HandleAppearance;
use Pirsch\Http\Middleware\TrackPageview;
use App\Http\Middleware\ClientHintsHeaders;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->statefulApi();
        $middleware->encryptCookies(except: ['appearance', 'sidebar_state']);

        $middleware->web(
            append: [
                //                TrackPageview::class,
                HandleAppearance::class,
                ClientHintsHeaders::class,
                HandleInertiaRequests::class,
                AddLinkHeadersForPreloadedAssets::class,
            ],
            prepend: [
                StripCookies::class,
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Throwable $e, $request) {
            return (new \App\Exceptions\Handler(app()))->render($request, $e);
        });
    })->create();
