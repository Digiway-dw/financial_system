<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for performance optimization features.
    |
    */

    'enable_query_logging' => env('ENABLE_QUERY_LOGGING', false),
    
    'slow_request_threshold' => env('SLOW_REQUEST_THRESHOLD', 1000), // milliseconds
    
    'cache_views' => env('CACHE_VIEWS', true),
    
    'optimize_images' => env('OPTIMIZE_IMAGES', true),
    
    'database_optimizations' => [
        'enable_query_cache' => env('DB_QUERY_CACHE', true),
        'connection_timeout' => env('DB_CONNECTION_TIMEOUT', 30),
        'max_connections' => env('DB_MAX_CONNECTIONS', 100),
    ],

    'memory_limits' => [
        'soft_limit' => env('MEMORY_SOFT_LIMIT', '256M'),
        'hard_limit' => env('MEMORY_HARD_LIMIT', '512M'),
    ],

    'security' => [
        'enable_rate_limiting' => env('ENABLE_RATE_LIMITING', true),
        'max_requests_per_minute' => env('MAX_REQUESTS_PER_MINUTE', 60),
        'enable_csrf_protection' => env('ENABLE_CSRF_PROTECTION', true),
    ],
];
