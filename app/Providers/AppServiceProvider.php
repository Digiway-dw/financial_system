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
                Log::channel('daily')->info('Query executed', [
                    'sql' => $query->sql,
                    'bindings' => $query->bindings,
                    'time' => $query->time . 'ms'
                ]);
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
            Log::warning('Database configuration failed: ' . $e->getMessage());
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
            Log::warning('URL configuration failed: ' . $e->getMessage());
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
        }

        if (request() && ($host = request()->getSchemeAndHttpHost())) {
            // Only override if it's not the default Laravel dev server
            if (!str_contains($host, '127.0.0.1:8000')) {
                URL::forceRootUrl($host);
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

        // String macro for money formatting
        Str::macro('money', function ($amount, $currency = 'SDG') {
            return number_format($amount, 2) . ' ' . $currency;
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
            Log::warning('View composer registration failed: ' . $e->getMessage());
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
            Log::warning('Icon configuration failed: ' . $e->getMessage());
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
