<?php

namespace App\Providers;

use App\Domain\Entities\User;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Customer;
use App\Policies\UserPolicy;
use App\Policies\AgentPolicy;
use App\Policies\CustomerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Transaction::class => AgentPolicy::class,
        Line::class => AgentPolicy::class,
        Customer::class => CustomerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Working hours management
        Gate::define('manage-working-hours', function ($user) {
            return $user->hasRole('admin');
        });
    }
}
