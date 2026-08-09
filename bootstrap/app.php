<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust proxies so URLs resolve to HTTPS behind Vercel / serverless edge.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // TEMP DEBUG: surface exception text in the response for Vercel diagnostics.
        $exceptions->render(function (Throwable $e, Request $request) {
            return response(get_class($e) . ': ' . $e->getMessage(), 500);
        });
    })->create();
