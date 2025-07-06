<?php

if (!function_exists('icon')) {
    function icon(string $name, array $attributes = []): string
    {
        return app('App\\Helpers\\IconHelper')->render($name, $attributes);
    }
} 