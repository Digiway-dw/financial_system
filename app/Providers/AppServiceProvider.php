<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Gate;
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

class AppServiceProvider extends ServiceProvider
{
    /**
     * All of the container singletons that should be registered.
     *
     * @var array
     */
    public $singletons = [
        // Define singletons here for better performance
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->registerEnvironmentSpecificServices();
        $this->registerMacros();
        $this->registerHelpers();
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
        $this->configureDatabase();
        $this->configureUrl();
        $this->configureModel();
        $this->configureValidation();
        $this->configureIcons();
        $this->configureBladeComponents();
        $this->shareGlobalViewData();

        // Register Blade Components
        Blade::component('danger-button', \Illuminate\View\Component::class);
        Blade::component('secondary-button', \Illuminate\View\Component::class);
        Blade::component('modal', \Illuminate\View\Component::class);

        // 🔧 Let the proper URL configuration method handle schemes and URLs

        // Add current_password validation rule
        \Illuminate\Support\Facades\Validator::extend('current_password', function ($attribute, $value, $parameters, $validator) {
            return \Illuminate\Support\Facades\Hash::check($value, \Illuminate\Support\Facades\Auth::user()->password);
        }, 'The :attribute does not match your current password.');

        // صلاحيات المستخدمين
        Gate::define('manage-users', function (\App\Domain\Entities\User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('manage-lines', function (\App\Domain\Entities\User $user) {
            return $user->hasRole('admin') || $user->hasRole('general_supervisor');
        });

        Gate::define('manage-safes', function (\App\Domain\Entities\User $user) {
            return $user->hasRole('admin') || $user->hasRole('branch_manager') || $user->hasRole('general_supervisor');
        });

        Gate::define('view-reports', function (\App\Domain\Entities\User $user) {
            return $user->hasRole('admin') || $user->hasRole('general_supervisor') || $user->hasRole('auditor');
        });
    }

    /**
     * Register environment-specific services.
     */
    private function registerEnvironmentSpecificServices(): void
    {
        match ($this->app->environment()) {
            'local' => $this->registerDevelopmentServices(),
            'testing' => $this->registerTestingServices(),
            'production' => $this->registerProductionServices(),
            default => null,
        };
    }

    /**
     * Register services only needed in development.
     */
    private function registerDevelopmentServices(): void
    {
        // Enable query logging in development
        if (config('app.debug')) {
            DB::listen(function ($query) {
               
            });
        }

        // Register development-only services if they exist
        if (class_exists('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider')) {
            $this->app->register('Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider');
        }
    }

    /**
     * Register services for testing environment.
     */
    private function registerTestingServices(): void
    {
        // Disable query logging in testing for performance
        config(['logging.channels.single.level' => 'error']);

        // Use in-memory database for faster tests
        config(['database.default' => 'sqlite']);
        config(['database.connections.sqlite.database' => ':memory:']);
    }

    /**
     * Register services for production environment.
     */
    private function registerProductionServices(): void
    {
        // Production-specific optimizations

        // Enable query result caching
        config(['cache.default' => 'redis']);

        // Set strict database settings
        config([
            'database.connections.mysql.strict' => true,
            'database.connections.mysql.modes' => [
                'ONLY_FULL_GROUP_BY',
                'STRICT_TRANS_TABLES',
                'NO_ZERO_IN_DATE',
                'NO_ZERO_DATE',
                'ERROR_FOR_DIVISION_BY_ZERO',
                'NO_AUTO_CREATE_USER',
                'NO_ENGINE_SUBSTITUTION',
            ],
        ]);
    }

    /**
     * Configure database settings.
     */
    private function configureDatabase(): void
    {
        try {
            // Set default string length for MySQL compatibility
            Schema::defaultStringLength(191);

            // Prevent lazy loading violations in non-production
            if (!$this->app->environment('production')) {
                Model::preventLazyLoading();
                Model::preventSilentlyDiscardingAttributes();
                Model::preventAccessingMissingAttributes();
            }

            // Configure database query timeouts
            config(['database.connections.mysql.options' => [
                \PDO::ATTR_TIMEOUT => 30,
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            ]]);
        } catch (\Exception $e) {
        }
    }

    /**
     * Configure URL generation based on environment.
     */
    private function configureUrl(): void
    {
        try {
            // Environment-specific URL configuration
            match ($this->app->environment()) {
                'production' => $this->configureProductionUrl(),
                'local' => $this->configureLocalUrl(),
                'staging' => $this->configureStagingUrl(),
                default => null,
            };
        } catch (\Exception $e) {
        }
    }

    /**
     * Configure production URL settings.
     */
    private function configureProductionUrl(): void
    {
        URL::forceScheme('https');

        // Force root URL if specified
        if ($rootUrl = config('app.force_url')) {
            URL::forceRootUrl($rootUrl);
        }
    }

    /**
     * Configure local development URL settings.
     */
    private function configureLocalUrl(): void
    {
        // Support for development tools like ngrok, Expose, etc.
        if (request() && request()->hasHeader('X-Forwarded-Proto')) {
            URL::forceScheme(request()->header('X-Forwarded-Proto'));
        } elseif (request() && request()->isSecure()) {
            // If the request is already secure (HTTPS), force HTTPS
            URL::forceScheme('https');
        }

        if (request() && ($host = request()->getSchemeAndHttpHost())) {
            // Only override if it's not the default Laravel dev server
            if (!str_contains($host, '127.0.0.1:8000') && !str_contains($host, 'localhost:8000')) {
                URL::forceRootUrl($host);

                // Force built assets when using external URLs like ngrok
                if (str_contains($host, 'ngrok') || str_contains($host, 'localtunnel') || str_contains($host, 'expose')) {
                    putenv('VITE_DEV_SERVER_KEY=');
                    config(['app.asset.version' => time()]);
                }
            }
        }
    }

    /**
     * Configure staging environment URL settings.
     */
    private function configureStagingUrl(): void
    {
        URL::forceScheme('https');
    }

    /**
     * Configure Eloquent model settings.
     */
    private function configureModel(): void
    {
        // Globally disable mass assignment protection in development
        if ($this->app->environment('local', 'testing')) {
            Model::unguard();
        }

        // Enable strict mode for better error detection
        if (!$this->app->environment('production')) {
            Model::shouldBeStrict();
        }

        // Enable lazy loading for all models
        Model::preventLazyLoading(false);
    }

    /**
     * Configure custom validation rules.
     */
    private function configureValidation(): void
    {
        // Custom validation rule for Sudanese phone numbers
        Validator::extend('sudanese_phone', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^(\+249|0)?[19]\d{8}$/', $value);
        });

        // Custom validation rule for branch codes
        Validator::extend('branch_code', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^BR\d{3}$/', $value);
        });

        // Custom validation rule for safe amounts
        Validator::extend('safe_amount', function ($attribute, $value, $parameters, $validator) {
            return is_numeric($value) && $value >= 0 && $value <= 999999999.99;
        });
    }

    /**
     * Register helpful macros.
     */
    private function registerMacros(): void
    {
        // Collection macro for financial calculations
        Collection::macro('sumMoney', function () {
            /** @var Collection $this */
            return $this->sum(function ($item) {
                return is_array($item) ? ($item['amount'] ?? 0) : ($item->amount ?? 0);
            });
        });

        // Request macro for financial system specific methods
        Request::macro('isFinancialRequest', function () {
            /** @var Request $this */
            return in_array($this->path(), [
                'transactions',
                'safes',
                'lines',
                'customers'
            ]);
        });

        // String macro for money formatting (integer-only, no decimals)
        Str::macro('money', function ($amount, $currency = 'SDG') {
            return number_format($amount) . ' ' . $currency;
        });
    }

    /**
     * Share global data with all views.
     */
    private function shareGlobalViewData(): void
    {
        try {
            // Share common data with all views
            View::composer('*', function ($view) {
                $view->with([
                    'appName' => config('app.name'),
                    'appVersion' => config('app.version', '1.0.0'),
                    'currentYear' => date('Y'),
                    'isProduction' => app()->environment('production'),
                ]);
            });

            // Share user-specific data with authenticated views
            View::composer(['dashboard.*', 'transactions.*', 'safes.*'], function ($view) {
                if (Auth::check()) {
                    $user = Auth::user();
                    $viewData = [
                        'userName' => $user->name ?? '',
                        'userEmail' => $user->email ?? '',
                        'userId' => $user->id ?? null,
                    ];

                    // Add branch information if available
                    if (isset($user->branch)) {
                        $viewData['userBranch'] = $user->branch?->name ?? 'No Branch';
                    }

                    $view->with($viewData);
                }
            });
        } catch (\Exception $e) {
        }
    }

    /**
     * Configure icon components and aliases.
     */
    private function configureIcons(): void
    {
        try {
            // Register common icon aliases for financial system
            $iconAliases = [
                'money' => 'heroicon-o-currency-dollar',
                'bank' => 'heroicon-o-building-library',
                'safe' => 'heroicon-o-lock-closed',
                'transaction' => 'heroicon-o-arrow-right-left',
                'user' => 'heroicon-o-user',
                'branch' => 'heroicon-o-building-office',
                'customer' => 'heroicon-o-users',
                'report' => 'heroicon-o-document-chart-bar',
                'dashboard' => 'heroicon-o-home',
                'settings' => 'heroicon-o-cog-6-tooth',
                'logout' => 'heroicon-o-arrow-right-on-rectangle',
                'login' => 'heroicon-o-arrow-left-on-rectangle',
                'success' => 'heroicon-o-check-circle',
                'error' => 'heroicon-o-x-circle',
                'warning' => 'heroicon-o-exclamation-triangle',
                'info' => 'heroicon-o-information-circle',
                'plus' => 'heroicon-o-plus',
                'minus' => 'heroicon-o-minus',
                'edit' => 'heroicon-o-pencil',
                'delete' => 'heroicon-o-trash',
                'view' => 'heroicon-o-eye',
                'approve' => 'heroicon-o-check',
                'reject' => 'heroicon-o-x-mark',
                'pending' => 'heroicon-o-clock',
                'search' => 'heroicon-o-magnifying-glass',
                'filter' => 'heroicon-o-funnel',
                'export' => 'heroicon-o-arrow-down-tray',
                'import' => 'heroicon-o-arrow-up-tray',
                'print' => 'heroicon-o-printer',
                'email' => 'heroicon-o-envelope',
                'phone' => 'heroicon-o-phone',
                'calendar' => 'heroicon-o-calendar-days',
                'time' => 'heroicon-o-clock',
                'location' => 'heroicon-o-map-pin',
            ];

            // Register icon aliases as Blade components
            foreach ($iconAliases as $alias => $iconName) {
                // Create blade directive for each alias
                Blade::component($iconName, $alias . '-icon');
            }
        } catch (\Exception $e) {
        }
    }

    /**
     * Register helper classes.
     */
    private function registerHelpers(): void
    {
        $this->app->singleton('App\Helpers\IconHelper', function ($app) {
            return new \App\Helpers\IconHelper();
        });

        $this->app->singleton('App\Helpers\RoleUiHelper', function ($app) {
            return new \App\Helpers\RoleUiHelper();
        });
    }

    /**
     * Configure Blade components.
     */
    private function configureBladeComponents(): void
    {
        // Register core components with explicit class names
        Blade::component('App\View\Components\ConfirmationModal', 'confirmation-modal');
        Blade::component('App\View\Components\Modal', 'modal');
        Blade::component('App\View\Components\Button', 'button');
        Blade::component('App\View\Components\SecondaryButton', 'secondary-button');
        Blade::component('App\View\Components\DangerButton', 'danger-button');

        // Custom Blade directive for forcing built assets when using ngrok
        Blade::directive('viteBuilt', function ($expression) {
            return "<?php
                \$manifestPath = public_path('build/manifest.json');
                if (file_exists(\$manifestPath)) {
                    \$manifest = json_decode(file_get_contents(\$manifestPath), true);
                    \$assets = $expression;
                    foreach (\$assets as \$asset) {
                        if (isset(\$manifest[\$asset])) {
                            if (str_contains(\$asset, '.css')) {
                                echo '<link rel=\"stylesheet\" href=\"' . asset('build/' . \$manifest[\$asset]['file']) . '\">';
                            } else {
                                echo '<script type=\"module\" src=\"' . asset('build/' . \$manifest[\$asset]['file']) . '\"></script>';
                            }
                        }
                    }
                } else {
                    echo '<!-- Vite manifest not found -->';
                }
            ?>";
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        return [];
    }
}
