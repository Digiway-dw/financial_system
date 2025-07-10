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
            'branch_code' => 'CAI001',
            'description' => 'Test branch for Cairo',
        ]);

        $roles = ['Admin', 'Agent', 'Supervisor', 'Branch Manager', 'Trainee'];
        foreach ($roles as $role) {
            $user = User::firstOrCreate(
                ['email' => strtolower(str_replace(' ', '_', $role)) . '@example.com'],
                [
                    'name' => $role . ' User',
                    'password' => Hash::make('password'),
                    'branch_id' => $branch->id,
                ]
            );
            $user->assignRole($role);
        }
    }
}
