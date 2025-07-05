<?php

namespace App\Providers;

use App\Constants\Roles;
use App\Domain\Entities\User as DomainUser;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register authorization gates.
     */
    public function boot(): void
    {
        // Register User policy for Domain\Entities\User
        Gate::policy(DomainUser::class, UserPolicy::class);
        
        Gate::define('manage-lines', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('manage-safes', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('view-reports', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::AUDITOR);
        });

        Gate::define('manage-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        Gate::define('manage-customers', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT);
        });

        Gate::define('manage-users', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        Gate::define('manage-branches', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) || $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });
    }
}
