<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;

class BladeComponentServiceProvider extends ServiceProvider
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
        $this->registerIconComponents();
        $this->registerCustomComponents();
        $this->registerBladeDirectives();
    }

    /**
     * Register icon components with fallbacks.
     */
    private function registerIconComponents(): void
    {
        // Create a fallback component for missing icons
        Blade::component('app.components.icon-fallback', 'icon-fallback');

        // Financial system specific icons
        $financialIcons = [
            'currency-dollar' => 'money',
            'building-library' => 'bank',
            'lock-closed' => 'safe',
            'arrow-right-left' => 'transaction',
            'users' => 'customers',
            'building-office' => 'branch',
            'chart-bar' => 'reports',
            'home' => 'dashboard',
        ];

        foreach ($financialIcons as $heroicon => $alias) {
            $this->registerIconWithFallback("heroicon-o-{$heroicon}", $alias);
        }
    }

    /**
     * Register icon with fallback handling.
     */
    private function registerIconWithFallback(string $iconComponent, string $alias): void
    {
        try {
            // Try to register the heroicon component
            if (View::exists("components.{$iconComponent}")) {
                Blade::component($iconComponent, $alias);
            } else {
                // Fallback to a simple icon or text
                Blade::component('app.components.icon-fallback', $alias);
            }
        } catch (\Exception $e) {
            // Create a simple text fallback
            Blade::directive($alias, function () use ($alias) {
                return "<?php echo '<span class=\"icon-{$alias}\">[{$alias}]</span>'; ?>";
            });
        }
    }

    /**
     * Register custom Blade components.
     */
    private function registerCustomComponents(): void
    {
        // Register common UI components
        Blade::component('app.components.alert', 'alert');
        Blade::component('app.components.button', 'button');
        Blade::component('app.components.card', 'card');
        Blade::component('app.components.modal', 'modal');
        Blade::component('app.components.form.input', 'input');
        Blade::component('app.components.form.select', 'select');
        Blade::component('app.components.form.textarea', 'textarea');
    }

    /**
     * Register custom Blade directives.
     */
    private function registerBladeDirectives(): void
    {
        // Money formatting directive
        Blade::directive('money', function ($expression) {
            return "<?php echo number_format({$expression}, 2) . ' SDG'; ?>";
        });

        // Date formatting directive
        Blade::directive('dateFormat', function ($expression) {
            return "<?php echo ({$expression})?->format('Y-m-d H:i:s') ?? 'N/A'; ?>";
        });

        // Status badge directive
        Blade::directive('statusBadge', function ($expression) {
            return "<?php echo app('App\\\\Helpers\\\\StatusHelper')->getBadge({$expression}); ?>";
        });

        // Permission check directive
        Blade::directive('canManage', function ($expression) {
            return "<?php if(auth()->check() && auth()->user()->can('manage-' . {$expression})): ?>";
        });

        Blade::directive('endcanManage', function () {
            return "<?php endif; ?>";
        });

        // Icon directive with fallback
        Blade::directive('icon', function ($expression) {
            return "<?php echo app('App\\\\Helpers\\\\IconHelper')->render({$expression}); ?>";
        });
    }
}
