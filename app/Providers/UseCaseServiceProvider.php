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
use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use Illuminate\Support\ServiceProvider;

class UseCaseServiceProvider extends ServiceProvider
{
    /**
     * Register use case bindings.
     */
    public function register(): void
    {
        $this->registerTransactionUseCases();
        $this->registerLineUseCases();
        $this->registerBranchUseCases();
    }

    private function registerTransactionUseCases(): void
    {
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
    }

    private function registerLineUseCases(): void
    {
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
    }

    private function registerBranchUseCases(): void
    {
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
}
