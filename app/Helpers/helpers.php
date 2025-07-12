<?php

if (!function_exists('icon')) {
    function icon(string $name, array $attributes = []): string
    {
        return app('App\\Helpers\\IconHelper')->render($name, $attributes);
    }
} 

if (!function_exists('generate_reference_number')) {
    function generate_reference_number($branchName)
    {
        $branch = str_replace(' ', '', $branchName);
        $unique = substr(str_pad((string) ((int) (microtime(true) * 1000)), 6, '0', STR_PAD_LEFT), -6);
        return $branch . '-' . $unique;
    }
} 