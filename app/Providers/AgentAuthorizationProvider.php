<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\Router;
use App\Http\Middleware\AgentDashboardAccess;

class AgentAuthorizationProvider extends ServiceProvider
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
        $router = $this->app->make(Router::class);
        $router->pushMiddlewareToGroup('web', AgentDashboardAccess::class);
    }
}
