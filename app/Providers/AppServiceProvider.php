<?php

namespace App\Providers;

use App\Application\UseCases\ApproveTransaction;
use App\Application\UseCases\ListPendingTransactions;
use App\Application\UseCases\RejectTransaction;
use App\Application\UseCases\CreateLine;
use App\Application\UseCases\UpdateLine;
use App\Application\UseCases\DeleteLine;
use App\Application\UseCases\ListLines;
use App\Application\UseCases\ViewLineBalanceAndUsage;
use App\Application\UseCases\CreateBranch;
use App\Application\UseCases\UpdateBranch;
use App\Application\UseCases\DeleteBranch;
use App\Application\UseCases\ListBranches;
use App\Domain\Interfaces\BranchRepository;
use App\Domain\Interfaces\CustomerRepository;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\UserRepository;
use App\Infrastructure\Repositories\EloquentBranchRepository;
use App\Infrastructure\Repositories\EloquentCustomerRepository;
use App\Infrastructure\Repositories\EloquentLineRepository;
use App\Infrastructure\Repositories\EloquentSafeRepository;
use App\Infrastructure\Repositories\EloquentTransactionRepository;
use App\Infrastructure\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\Request;
use App\Models\User;

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
        $this->app->bind(BranchRepository::class, EloquentBranchRepository::class);

        // Use Case Bindings
        $this->app->singleton(ListPendingTransactions::class, function ($app) {
            return new ListPendingTransactions($app->make(TransactionRepository::class));
        });
        $this->app->singleton(ApproveTransaction::class, function ($app) {
            return new ApproveTransaction(
                $app->make(TransactionRepository::class),
                $app->make(SafeRepository::class),
                $app->make(LineRepository::class)
            );
        });
        $this->app->singleton(RejectTransaction::class, function ($app) {
            return new RejectTransaction(
                $app->make(TransactionRepository::class),
                $app->make(SafeRepository::class)
            );
        });

        $this->app->singleton(CreateLine::class, function ($app) {
            return new CreateLine($app->make(LineRepository::class));
        });
        $this->app->singleton(UpdateLine::class, function ($app) {
            return new UpdateLine($app->make(LineRepository::class));
        });
        $this->app->singleton(DeleteLine::class, function ($app) {
            return new DeleteLine($app->make(LineRepository::class));
        });
        $this->app->singleton(ListLines::class, function ($app) {
            return new ListLines(
                $app->make(LineRepository::class),
                $app->make(ViewLineBalanceAndUsage::class)
            );
        });
        $this->app->singleton(ViewLineBalanceAndUsage::class, function ($app) {
            return new ViewLineBalanceAndUsage(
                $app->make(LineRepository::class),
                $app->make(TransactionRepository::class)
            );
        });

        $this->app->singleton(CreateBranch::class, function ($app) {
            return new CreateBranch(
                $app->make(BranchRepository::class),
                $app->make(SafeRepository::class)
            );
        });
        $this->app->singleton(UpdateBranch::class, function ($app) {
            return new UpdateBranch($app->make(BranchRepository::class));
        });
        $this->app->singleton(DeleteBranch::class, function ($app) {
            return new DeleteBranch($app->make(BranchRepository::class));
        });
        $this->app->singleton(ListBranches::class, function ($app) {
            return new ListBranches($app->make(BranchRepository::class));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ يجبر Laravel يولد روابط https بدل http
        if (app()->environment('local')) {
            URL::forceScheme('https');
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }

        // 🔧 إجبار Laravel يولد روابط بناءً على عنوان الزيارة (ngrok أو غيره)
        if (app()->environment('local') && request()->getSchemeAndHttpHost()) {
            URL::forceRootUrl(request()->getSchemeAndHttpHost());
        }
    
        // صلاحيات المستخدمين
        Gate::define('manage-lines', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('general_supervisor');
        });

        Gate::define('manage-safes', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('branch_manager') || $user->hasRole('general_supervisor');
        });

        Gate::define('view-reports', function (User $user) {
            return $user->hasRole('admin') || $user->hasRole('general_supervisor') || $user->hasRole('auditor');
        });
    }
}
