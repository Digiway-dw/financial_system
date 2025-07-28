<?php

namespace Database\Seeders;

use App\Domain\Entities\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class EnsureAdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure all required roles exist using Constants\Roles
        $roles = [
            'admin',
            'agent',
            'branch_manager',
            'general_supervisor',
            'trainee',
            'auditor',
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role]);
        }

        // Get the first user or create one if none exists
        $user = User::first();

        if ($user) {
            // Assign admin role to the first user
            $user->syncRoles(['Admin']);
            $this->command->info('Admin role assigned to user: ' . $user->name);
        } else {
            $this->command->error('No users found in the database');
        }
    }
}
