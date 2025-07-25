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

        // Removed default admin@example.com account creation/check logic
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
