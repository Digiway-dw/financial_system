<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use App\Domain\Entities\User;
use App\Constants\Roles as RoleConstants;

class VerifyRolesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'system:verify-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify system roles and user role assignments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Verifying system roles...');

        // Compare constants with database roles
        $this->verifyRoleDefinitions();

        // Check role assignments
        $this->verifyRoleAssignments();

        // Verify at least one admin exists
        $this->verifyAdminExists();

        return Command::SUCCESS;
    }

    /**
     * Verify that roles defined in Constants\Roles match those in the database
     */
    private function verifyRoleDefinitions(): void
    {
        $this->info('Checking role definitions...');

        // Get roles from constants
        $constantRoles = RoleConstants::all();
        $this->line('Roles defined in Constants\Roles: ' . implode(', ', $constantRoles));

        // Get roles from database
        $dbRoles = Role::all()->pluck('name')->toArray();
        $this->line('Roles in database: ' . implode(', ', $dbRoles));

        // Check for missing roles (in constants but not in db)
        $missingRoles = array_diff($constantRoles, $dbRoles);
        if (!empty($missingRoles)) {
            $this->warn('Missing roles in database: ' . implode(', ', $missingRoles));
            $this->warn('Run "php artisan system:setup-roles" to fix this issue.');
        } else {
            $this->info('✓ All roles from Constants\Roles exist in the database.');
        }

        // Check for extra roles (in db but not in constants)
        $extraRoles = array_diff($dbRoles, $constantRoles);
        if (!empty($extraRoles)) {
            $this->warn('Extra roles in database (not defined in Constants\Roles): ' . implode(', ', $extraRoles));
        } else {
            $this->info('✓ No extra roles found in the database.');
        }
    }

    /**
     * Verify role assignments to users
     */
    private function verifyRoleAssignments(): void
    {
        $this->newLine();
        $this->info('Checking role assignments...');

        // Get all users with their roles
        $users = User::all();

        if ($users->isEmpty()) {
            $this->warn('No users found in the database.');
            return;
        }

        $this->line('User role assignments:');

        $headers = ['ID', 'Name', 'Email', 'Roles'];
        $rows = [];

        foreach ($users as $user) {
            $roles = $user->getRoleNames()->toArray();
            $rows[] = [
                $user->id,
                $user->name,
                $user->email,
                implode(', ', $roles),
            ];
        }

        $this->table($headers, $rows);

        // Count users by role
        $roleCounts = [];
        foreach (RoleConstants::all() as $role) {
            $count = User::role($role)->count();
            $roleCounts[$role] = $count;
        }

        $this->line('Users per role:');
        foreach ($roleCounts as $role => $count) {
            $this->line(" - {$role}: {$count} users");
        }
    }

    /**
     * Verify at least one admin exists
     */
    private function verifyAdminExists(): void
    {
        $this->newLine();
        $this->info('Checking admin users...');

        $adminCount = User::role(RoleConstants::ADMIN)->count();

        if ($adminCount === 0) {
            $this->error('❌ No admin users found! This is a critical issue.');
            $this->line('Run "php artisan system:setup-roles" to create an admin user.');
        } else {
            $this->info("✓ Found {$adminCount} admin users.");

            // List admin users
            $admins = User::role(RoleConstants::ADMIN)->get(['id', 'name', 'email']);
            $this->line('Admin users:');

            $headers = ['ID', 'Name', 'Email'];
            $rows = [];

            foreach ($admins as $admin) {
                $rows[] = [
                    $admin->id,
                    $admin->name,
                    $admin->email,
                ];
            }

            $this->table($headers, $rows);
        }
    }
}
