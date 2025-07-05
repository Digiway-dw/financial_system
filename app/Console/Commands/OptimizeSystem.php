<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class OptimizeSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'system:optimize 
                            {--clear-cache : Clear all caches}
                            {--optimize-db : Optimize database}
                            {--optimize-assets : Optimize frontend assets}
                            {--all : Run all optimizations}';

    /**
     * The console command description.
     */
    protected $description = 'Optimize the financial system for better performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting Financial System Optimization...');

        if ($this->option('all') || $this->option('clear-cache')) {
            $this->clearCaches();
        }

        if ($this->option('all') || $this->option('optimize-db')) {
            $this->optimizeDatabase();
        }

        if ($this->option('all') || $this->option('optimize-assets')) {
            $this->optimizeAssets();
        }

        if ($this->option('all')) {
            $this->runGeneralOptimizations();
        }

        $this->info('✅ System optimization completed successfully!');
        return self::SUCCESS;
    }

    /**
     * Clear all application caches.
     */
    private function clearCaches(): void
    {
        $this->info('🧹 Clearing caches...');

        $caches = [
            'config:clear' => 'Configuration cache',
            'route:clear' => 'Route cache',
            'view:clear' => 'View cache',
            'cache:clear' => 'Application cache',
            'event:clear' => 'Event cache',
        ];

        foreach ($caches as $command => $description) {
            $this->line("   Clearing {$description}...");
            Artisan::call($command);
        }

        $this->info('✅ All caches cleared!');
    }

    /**
     * Optimize database performance.
     */
    private function optimizeDatabase(): void
    {
        $this->info('🗄️ Optimizing database...');

        try {
            // Run database optimizations
            DB::statement('OPTIMIZE TABLE users, branches, safes, transactions, customers');
            $this->line('   Database tables optimized');

            // Analyze tables for better query planning
            DB::statement('ANALYZE TABLE users, branches, safes, transactions, customers');
            $this->line('   Table statistics updated');

            $this->info('✅ Database optimization completed!');
        } catch (\Exception $e) {
            $this->error('❌ Database optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Optimize frontend assets.
     */
    private function optimizeAssets(): void
    {
        $this->info('🎨 Optimizing frontend assets...');

        try {
            // Build production assets
            $this->line('   Building production assets...');
            exec('npm run build 2>&1', $output, $return);

            if ($return === 0) {
                $this->line('   Assets built successfully');
            } else {
                $this->warn('   Asset build had warnings: ' . implode("\n", $output));
            }

            $this->info('✅ Frontend assets optimized!');
        } catch (\Exception $e) {
            $this->error('❌ Asset optimization failed: ' . $e->getMessage());
        }
    }

    /**
     * Run general system optimizations.
     */
    private function runGeneralOptimizations(): void
    {
        $this->info('⚡ Running general optimizations...');

        $optimizations = [
            'config:cache' => 'Configuration caching',
            'route:cache' => 'Route caching',
            'view:cache' => 'View caching',
            'event:cache' => 'Event caching',
        ];

        foreach ($optimizations as $command => $description) {
            $this->line("   {$description}...");
            Artisan::call($command);
        }

        // Clean up temporary files
        $this->cleanupTempFiles();

        $this->info('✅ General optimizations completed!');
    }

    /**
     * Clean up temporary files.
     */
    private function cleanupTempFiles(): void
    {
        $this->line('   Cleaning up temporary files...');

        $tempPaths = [
            storage_path('logs/*.log'),
            storage_path('framework/cache/data/*'),
            storage_path('framework/sessions/*'),
            storage_path('framework/views/*'),
        ];

        foreach ($tempPaths as $path) {
            $files = glob($path);
            foreach ($files as $file) {
                if (is_file($file) && time() - filemtime($file) > 86400) { // Older than 24 hours
                    unlink($file);
                }
            }
        }
    }
}
