<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Constants\Roles;
use Spatie\Permission\Models\Role;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\Hash;

class FinancialSystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure all system roles from Constants\Roles exist
        $this->ensureSystemRolesExist();

        // Create demo users for each role
        $this->createDemoUsers();
    }

    /**
     * Ensure all system roles defined in Constants\Roles exist in the database
     */
    private function ensureSystemRolesExist(): void
    {
        $this->command->info('Ensuring all system roles exist in the database...');

        // Get all roles from the Constants\Roles class
        $systemRoles = Roles::all();

        // Role descriptions
        $descriptions = [
            Roles::ADMIN => 'Full system access with unrestricted cash withdrawal',
            Roles::GENERAL_SUPERVISOR => 'Cross-branch oversight without role management',
            Roles::AUDITOR => 'Transaction approval and audit capabilities',
            Roles::BRANCH_MANAGER => 'Branch-level management and approvals',
            Roles::AGENT => 'Transaction processing with restricted withdrawals',
            Roles::TRAINEE => 'Supervised transaction processing requiring approval',
        ];

        // Create or update each role
        foreach ($systemRoles as $roleName) {
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                ['description' => $descriptions[$roleName] ?? '']
            );

            $this->command->info("Role '{$roleName}' ensured in database.");
        }

        // Check for any roles in the database that are not in our Constants\Roles
        $dbRoles = Role::all()->pluck('name')->toArray();
        $extraRoles = array_diff($dbRoles, $systemRoles);

        if (!empty($extraRoles)) {
            $this->command->warn('Found extra roles in database that are not defined in Constants\Roles:');
            foreach ($extraRoles as $extraRole) {
                $this->command->warn(" - {$extraRole}");
            }
        }
    }

    /**
     * Create demo users for each role
     */
    private function createDemoUsers(): void
    {
        $this->command->info('Creating demo users for each role...');

        $demoUsers = [
            [
                'name' => 'Admin User',
                'email' => 'admin@financial.system',
                'role' => Roles::ADMIN,
            ],
            [
                'name' => 'General Supervisor',
                'email' => 'supervisor@financial.system',
                'role' => Roles::GENERAL_SUPERVISOR,
            ],
            [
                'name' => 'Auditor User',
                'email' => 'auditor@financial.system',
                'role' => Roles::AUDITOR,
            ],
            [
                'name' => 'Branch Manager',
                'email' => 'manager@financial.system',
                'role' => Roles::BRANCH_MANAGER,
            ],
            [
                'name' => 'Agent User',
                'email' => 'agent@financial.system',
                'role' => Roles::AGENT,
            ],
            [
                'name' => 'Trainee User',
                'email' => 'trainee@financial.system',
                'role' => Roles::TRAINEE,
            ],
        ];

        // Create or update each demo user
        foreach ($demoUsers as $userData) {
            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]
            );

            // Assign role to user
            $user->syncRoles([$userData['role']]);

            $this->command->info("Demo user '{$userData['name']}' created with role '{$userData['role']}'.");
        }
    }
}
