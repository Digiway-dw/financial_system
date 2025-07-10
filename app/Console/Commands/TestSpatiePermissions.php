<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Route;

class TestSpatiePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:spatie-permissions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test if Spatie Permission package is set up correctly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Spatie Permission Package Setup');
        $this->line('----------------------------------------');

        // Test 1: Check if roles table exists
        try {
            $roleCount = Role::count();
            $this->info("✅ Roles table exists with {$roleCount} roles");
        } catch (\Exception $e) {
            $this->error("❌ Roles table check failed: " . $e->getMessage());
        }

        // Test 2: Check if permissions table exists
        try {
            $permissionCount = Permission::count();
            $this->info("✅ Permissions table exists with {$permissionCount} permissions");
        } catch (\Exception $e) {
            $this->error("❌ Permissions table check failed: " . $e->getMessage());
        }

        // Test 3: Check if the role middleware is registered
        try {
            $hasRoleMiddleware = collect(Route::getMiddleware())->has('role');
            if ($hasRoleMiddleware) {
                $this->info("✅ Role middleware is registered");
            } else {
                $this->error("❌ Role middleware is not registered");
            }
        } catch (\Exception $e) {
            $this->error("❌ Role middleware check failed: " . $e->getMessage());
        }

        // Test 4: Check if the permission middleware is registered
        try {
            $hasPermissionMiddleware = collect(Route::getMiddleware())->has('permission');
            if ($hasPermissionMiddleware) {
                $this->info("✅ Permission middleware is registered");
            } else {
                $this->error("❌ Permission middleware is not registered");
            }
        } catch (\Exception $e) {
            $this->error("❌ Permission middleware check failed: " . $e->getMessage());
        }

        // Test 5: Check if the role_or_permission middleware is registered
        try {
            $hasRoleOrPermissionMiddleware = collect(Route::getMiddleware())->has('role_or_permission');
            if ($hasRoleOrPermissionMiddleware) {
                $this->info("✅ Role or Permission middleware is registered");
            } else {
                $this->error("❌ Role or Permission middleware is not registered");
            }
        } catch (\Exception $e) {
            $this->error("❌ Role or Permission middleware check failed: " . $e->getMessage());
        }

        // Final summary
        $this->line('----------------------------------------');
        $this->info('Spatie Permission package test completed');

        return 0;
    }
}
