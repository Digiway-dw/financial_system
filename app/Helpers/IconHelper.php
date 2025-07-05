<?php

namespace App\Helpers;

use Illuminate\Support\Facades\View;

class IconHelper
{
    /**
     * Icon mapping for the financial system.
     */
    private static array $iconMap = [
        'money' => 'heroicon-o-currency-dollar',
        'bank' => 'heroicon-o-building-library',
        'safe' => 'heroicon-o-lock-closed',
        'transaction' => 'heroicon-o-arrow-right-left',
        'user' => 'heroicon-o-user',
        'users' => 'heroicon-o-users',
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

    /**
     * Render an icon with fallback.
     */
    public static function render(string $name, array $attributes = []): string
    {
        $iconName = self::$iconMap[$name] ?? $name;
        
        // Check if the heroicon component exists
        if (self::componentExists($iconName)) {
            return self::renderHeroicon($iconName, $attributes);
        }
        
        // Fallback to SVG icon
        return self::renderFallbackIcon($name, $attributes);
    }

    /**
     * Check if a component exists.
     */
    private static function componentExists(string $componentName): bool
    {
        try {
            return View::exists("components.{$componentName}");
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Render heroicon component.
     */
    private static function renderHeroicon(string $iconName, array $attributes): string
    {
        $attributesString = self::attributesToString($attributes);
        return "<x-{$iconName}{$attributesString} />";
    }

    /**
     * Render fallback icon.
     */
    private static function renderFallbackIcon(string $name, array $attributes): string
    {
        $class = $attributes['class'] ?? 'w-5 h-5';
        $title = $attributes['title'] ?? ucfirst($name);
        
        // Simple SVG fallback based on icon type
        $svg = self::getFallbackSvg($name);
        
        return "<span class=\"inline-block {$class}\" title=\"{$title}\">{$svg}</span>";
    }

    /**
     * Get fallback SVG for common icons.
     */
    private static function getFallbackSvg(string $name): string
    {
        $svgs = [
            'money' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C13.1 2 14 2.9 14 4C14 5.1 13.1 6 12 6C10.9 6 10 5.1 10 4C10 2.9 10.9 2 12 2ZM21 9V7L15 4L12 5.69L9 4L3 7V9C3 9.55 3.45 10 4 10H20C20.55 10 21 9.55 21 9ZM3 19C3 19.55 3.45 20 4 20H20C20.55 20 21 19.55 21 19V11H3V19Z"/></svg>',
            'user' => '<svg viewBox="0 0 24 24" fill="currentColor"><path d="M12 12C14.21 12 16 10.21 16 8S14.21 4 12 4 8 5.79 8 8 9.79 12 12 12M12 14C9.33 14 4 15.34 4 18V20H20V18C20 15.34 14.67 14 12 14Z"/></svg>',
            'default' => '<svg viewBox="0 0 24 24" fill="currentColor"><circle cx="12" cy="12" r="10"/><path d="M9,9h6v6H9V9z"/></svg>',
        ];

        return $svgs[$name] ?? $svgs['default'];
    }

    /**
     * Convert attributes array to string.
     */
    private static function attributesToString(array $attributes): string
    {
        $result = '';
        foreach ($attributes as $key => $value) {
            $result .= " {$key}=\"{$value}\"";
        }
        return $result;
    }

    /**
     * Get all available icons.
     */
    public static function getAvailableIcons(): array
    {
        return array_keys(self::$iconMap);
    }

    /**
     * Check if an icon exists in the map.
     */
    public static function iconExists(string $name): bool
    {
        return isset(self::$iconMap[$name]);
    }
}
