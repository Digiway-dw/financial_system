<?php

namespace App\Providers;

use App\Domain\Interfaces\CustomerRepository;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\UserRepository;
use App\Infrastructure\Repositories\EloquentCustomerRepository;
use App\Infrastructure\Repositories\EloquentLineRepository;
use App\Infrastructure\Repositories\EloquentSafeRepository;
use App\Infrastructure\Repositories\EloquentTransactionRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(UserRepository::class, EloquentUserRepository::class);
        $this->app->bind(CustomerRepository::class, EloquentCustomerRepository::class);
        $this->app->bind(TransactionRepository::class, EloquentTransactionRepository::class);
        $this->app->bind(LineRepository::class, EloquentLineRepository::class);
        $this->app->bind(SafeRepository::class, EloquentSafeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
