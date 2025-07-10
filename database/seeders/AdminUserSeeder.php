<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Make sure admin role exists
        $adminRole = Role::where('name', 'admin')->first();

        if (!$adminRole) {
            $this->command->info('Creating admin role...');
            $adminRole = Role::create(['name' => 'admin']);

            // Add all permissions to admin role
            $permissions = \Spatie\Permission\Models\Permission::all();
            $adminRole->syncPermissions($permissions);
        }

        // Create or update admin user
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        // Assign admin role
        $admin->assignRole('admin');

        $this->command->info('Admin user created with:');
        $this->command->info('Email: admin@example.com');
        $this->command->info('Password: password');
    }
}
