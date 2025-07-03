<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Create Permissions
        // Admin Permissions
        Permission::firstOrCreate(['name' => 'perform-unrestricted-withdrawal']);
        Permission::firstOrCreate(['name' => 'approve-all-requests']);
        Permission::firstOrCreate(['name' => 'edit-all-data']);
        Permission::firstOrCreate(['name' => 'manage-users']);
        Permission::firstOrCreate(['name' => 'manage-branches']);
        Permission::firstOrCreate(['name' => 'manage-sim-lines']);
        Permission::firstOrCreate(['name' => 'view-all-reports']);
        Permission::firstOrCreate(['name' => 'manage-safes']);
        Permission::firstOrCreate(['name' => 'view-audit-log']);
        Permission::firstOrCreate(['name' => 'view-customers']);
        Permission::firstOrCreate(['name' => 'manage-customers']);
        Permission::firstOrCreate(['name' => 'view-lines']);
        Permission::firstOrCreate(['name' => 'view-branches']);

        // General Supervisor Permissions
        Permission::firstOrCreate(['name' => 'view-all-branches-data']);
        Permission::firstOrCreate(['name' => 'approve-transactions']);
        Permission::firstOrCreate(['name' => 'edit-daily-transactions']);

        // Auditor Permissions
        Permission::firstOrCreate(['name' => 'approve-pending-transactions']);
        Permission::firstOrCreate(['name' => 'edit-all-daily-transactions']);

        // Branch Manager Permissions
        Permission::firstOrCreate(['name' => 'view-own-branch-data']);
        Permission::firstOrCreate(['name' => 'approve-own-branch-transactions']);
        Permission::firstOrCreate(['name' => 'edit-own-branch-transactions']);
        Permission::firstOrCreate(['name' => 'view-client-reports']);
        Permission::firstOrCreate(['name' => 'view-pending-transactions']);
        Permission::firstOrCreate(['name' => 'manage-own-branch-safes']);

        // Branch Staff (Responsible) Permissions
        Permission::firstOrCreate(['name' => 'send-transfer']);
        Permission::firstOrCreate(['name' => 'receive-transfer']);
        Permission::firstOrCreate(['name' => 'search-transactions']);
        Permission::firstOrCreate(['name' => 'deposit-cash']);
        Permission::firstOrCreate(['name' => 'print-receipts']);

        // Trainee Permissions
        Permission::firstOrCreate(['name' => 'send-transfer-pending']);
        Permission::firstOrCreate(['name' => 'receive-transfer-pending']);

        // Create Roles and Assign Permissions

        // Admin Role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->givePermissionTo(
            'perform-unrestricted-withdrawal',
            'approve-all-requests',
            'edit-all-data',
            'manage-users',
            'manage-branches',
            'manage-sim-lines',
            'view-all-reports',
            'manage-safes',
            'view-audit-log',
            'approve-pending-transactions',
            'edit-all-daily-transactions',
            'view-all-branches-data',
            'approve-transactions',
            'edit-daily-transactions',
            'view-own-branch-data',
            'approve-own-branch-transactions',
            'edit-own-branch-transactions',
            'view-client-reports',
            'view-pending-transactions',
            'manage-own-branch-safes',
            'send-transfer',
            'receive-transfer',
            'search-transactions',
            'deposit-cash',
            'print-receipts',
            'send-transfer-pending',
            'receive-transfer-pending',
            'view-customers',
            'view-lines',
            'view-branches',
            'manage-customers'
        );

        // General Supervisor Role
        $generalSupervisorRole = Role::firstOrCreate(['name' => 'general_supervisor']);
        $generalSupervisorRole->givePermissionTo(
            'view-all-branches-data',
            'view-all-reports',
            'manage-safes',
            'approve-transactions',
            'edit-daily-transactions',
            'approve-pending-transactions',
            'view-audit-log',
            'view-customers',
            'view-lines',
            'view-branches',
            'manage-customers'
        );

        // Auditor Role
        $auditorRole = Role::firstOrCreate(['name' => 'auditor']);
        $auditorRole->givePermissionTo(
            'approve-pending-transactions',
            'edit-all-daily-transactions',
            'view-audit-log',
            'view-customers',
            'manage-customers'
        );

        // Branch Manager Role
        $branchManagerRole = Role::firstOrCreate(['name' => 'branch_manager']);
        $branchManagerRole->givePermissionTo(
            'view-own-branch-data',
            'approve-own-branch-transactions',
            'edit-own-branch-transactions',
            'view-client-reports',
            'view-pending-transactions',
            'manage-own-branch-safes',
            'approve-pending-transactions',
            'view-customers',
            'manage-customers'
        );

        // Branch Staff (Responsible) Role
        $agentRole = Role::firstOrCreate(['name' => 'agent']);
        $agentRole->givePermissionTo(
            'send-transfer',
            'receive-transfer',
            'search-transactions',
            'deposit-cash',
            'print-receipts',
            'view-lines',
            'manage-customers'
        );

        // Trainee Role
        $traineeRole = Role::firstOrCreate(['name' => 'trainee']);
        $traineeRole->givePermissionTo(
            'send-transfer-pending',
            'receive-transfer-pending',
            'view-lines'
        );
    }
}
