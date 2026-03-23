<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Register Global Web Middleware
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        // 2. Register Middleware Aliases
        // This links 'check.system' in your web.php to the actual file
        $middleware->alias([
            'check.system' => \App\Http\Middleware\CheckSystemStatus::class,
        ]);

        // 3. Configure Default Redirects
        $middleware->redirectTo(
            guests: '/login',
            users: '/dashboard',
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();