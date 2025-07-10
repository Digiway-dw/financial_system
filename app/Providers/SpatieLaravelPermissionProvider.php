<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Middleware;

class SpatieLaravelPermissionProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $router = $this->app->make('router');

        // Register the role middleware
        $router->aliasMiddleware('role', \Spatie\Permission\Middleware\RoleMiddleware::class);

        // Register the permission middleware
        $router->aliasMiddleware('permission', \Spatie\Permission\Middleware\PermissionMiddleware::class);

        // Register the role or permission middleware
        $router->aliasMiddleware('role_or_permission', \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class);
    }
}
