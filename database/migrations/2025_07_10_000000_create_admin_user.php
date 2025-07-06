<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Hash;
use App\Domain\Entities\User;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if admin role exists
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin']);

            // Add all permissions to admin role
            $permissions = \Spatie\Permission\Models\Permission::all();
            $adminRole->syncPermissions($permissions);
        }

        // Create admin user
        $admin = User::where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);

            // Assign admin role
            $admin->assignRole('admin');
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove admin user
        $admin = User::where('email', 'admin@example.com')->first();
        if ($admin) {
            $admin->delete();
        }
    }
};
