<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domain\Entities\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Ensure Cairo branch exists
        $branch = \App\Models\Domain\Entities\Branch::firstOrCreate([
            'name' => 'Cairo Branch',
            'location' => 'Cairo',
        ], [
            'branch_code' => 'CA001', // Fixed to match constraint: 2 letters + 3 digits
            'description' => 'Test branch for Cairo',
        ]);

        $roles = ['admin', 'agent', 'general_supervisor', 'branch_manager', 'trainee'];
        foreach ($roles as $role) {
            $user = User::firstOrCreate(
                ['email' => $role . '@example.com'],
                [
                    'name' => ucfirst(str_replace('_', ' ', $role)) . ' User',
                    'password' => Hash::make('password'),
                    'branch_id' => $branch->id,
                ]
            );
            $user->assignRole($role);
        }
    }
}
