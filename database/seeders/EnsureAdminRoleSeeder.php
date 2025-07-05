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
        // Make sure admin role exists
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        
        // Get the first user or create one if none exists
        $user = User::first();
        
        if ($user) {
            // Assign admin role to the first user
            $user->syncRoles([$adminRole->name]);
            $this->command->info('Admin role assigned to user: ' . $user->name);
        } else {
            $this->command->error('No users found in the database');
        }
    }
}
