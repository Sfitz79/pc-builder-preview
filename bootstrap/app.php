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

        // Seed the current-market catalogue on fresh deployments when enabled.
        $middleware->append(\App\Http\Middleware\EnsureCatalogSeeded::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
