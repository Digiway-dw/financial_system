<?php

namespace Illuminate\Foundation;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Add our middlewares to the global middleware stack
        $middleware->append(\App\Http\Middleware\BypassAuthorization::class);
        $middleware->append(\App\Http\Middleware\AgentDashboardAccess::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
