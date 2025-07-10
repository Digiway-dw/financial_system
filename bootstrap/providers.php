<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\RepositoryServiceProvider::class,
    App\Providers\UseCaseServiceProvider::class,
    App\Providers\AuthorizationServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\BladeComponentServiceProvider::class,
    App\Providers\VoltServiceProvider::class,
    App\Providers\AgentAuthorizationProvider::class,
    App\Providers\SpatieLaravelPermissionProvider::class,
    Spatie\Permission\PermissionServiceProvider::class,
];
