<?php

use App\Http\Middleware\CheckForAnyTokenAbilities;
use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Sentry\Laravel\Integration;
use Spatie\LaravelFlare\Facades\Flare;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web     : __DIR__ . '/../routes/web.php',
        api     : __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health  : '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->web(
            append: [
                HandleInertiaRequests::class,
                AddLinkHeadersForPreloadedAssets::class,
            ]
        );

        $middleware->alias(
            [
                'abilities' => CheckForAnyTokenAbilities::class,
            ]
        );

        $middleware->statefulApi();

    })
    ->withExceptions(function (Exceptions $exceptions) {
        Flare::handles($exceptions);
        Integration::handles($exceptions);
    })->create();
