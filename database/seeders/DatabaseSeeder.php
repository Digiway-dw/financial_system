<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(EnhancedPermissionsSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(EnsureAdminRoleSeeder::class);
        $this->call(LineSeeder::class);

        // Add the comprehensive financial system seeder
        $this->call(FinancialSystemSeeder::class);
    }
}
