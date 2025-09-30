<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PerformanceOptimization
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Start performance timer
        $startTime = microtime(true);

        // Add response headers for better caching
        $response = $next($request);

        // Calculate request duration
        $duration = (microtime(true) - $startTime) * 1000;

        // Log slow requests (over 1 second)
        if ($duration > 1000) {
           
        }

        // Add performance headers for development
        if (app()->environment('local')) {
            $response->headers->add([
                'X-Response-Time' => $duration . 'ms',
                'X-Memory-Usage' => round(memory_get_peak_usage(true) / 1024 / 1024, 2) . 'MB',
                'X-Queries-Count' => $this->getQueryCount(),
            ]);
        }

        return $response;
    }

    /**
     * Get the number of database queries executed.
     */
    private function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }
}
