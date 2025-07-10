<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Constants\Roles;

class RoleSeeder extends Seeder
{
    public function run()
    {
        // Use our role constants from the app
        $roles = Roles::all();

        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
            $this->command->info("Role '{$role}' created or confirmed.");
        }

        // Assign the admin role to at least one user if we have users
        $this->assignAdminRoleIfNeeded();
    }

    /**
     * Make sure at least one user has the admin role
     */
    private function assignAdminRoleIfNeeded()
    {
        // Check if any user has the admin role
        $adminRole = Role::where('name', Roles::ADMIN)->first();

        if (!$adminRole) {
            $this->command->error("Admin role not found!");
            return;
        }

        $hasAdmin = $adminRole->users()->exists();

        if (!$hasAdmin) {
            // Get the first user and assign admin role
            $user = \App\Domain\Entities\User::first();

            if ($user) {
                $user->assignRole(Roles::ADMIN);
                $this->command->info("Assigned admin role to user: {$user->name}");
            } else {
                $this->command->warn("No users found to assign admin role!");
            }
        } else {
            $this->command->info("Admin role is already assigned to at least one user.");
        }
    }
}
