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
        $branch = \App\Models\Domain\Entities\Branch::first();
        $roles = ['Admin', 'Agent', 'Supervisor', 'Branch Manager', 'Trainee'];
        foreach ($roles as $role) {
            $user = User::firstOrCreate(
                ['email' => strtolower($role) . '@example.com'],
                [
                    'name' => $role . ' User',
                    'password' => Hash::make('password'),
                    'branch_id' => $branch ? $branch->id : null,
                ]
            );
            $user->assignRole($role);
        }
    }
}
