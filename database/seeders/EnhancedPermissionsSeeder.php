<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EnhancedPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()['cache']->forget('spatie.permission.cache');

        // Define permission groups with their permissions and descriptions
        $permissionGroups = [
            'user_management' => [
                'create-users' => 'Ability to create new user accounts',
                'manage-users' => 'Ability to manage all user accounts',
            ],
            'financial_operations' => [
                'perform-unrestricted-withdrawal' => 'Ability to perform withdrawals without restrictions',
                'deposit-cash' => 'Ability to deposit cash',
                'send-transfer' => 'Ability to send transfers',
                'receive-transfer' => 'Ability to receive transfers',
                'send-transfer-pending' => 'Ability to create pending transfer requests',
                'receive-transfer-pending' => 'Ability to create pending receive requests',
            ],
            'approval_management' => [
                'approve-all-requests' => 'Ability to approve all types of requests',
                'approve-transactions' => 'Ability to approve transactions',
                'approve-pending-transactions' => 'Ability to approve pending transactions',
                'approve-own-branch-transactions' => 'Ability to approve transactions within own branch',
            ],
            'data_management' => [
                'edit-all-data' => 'Ability to edit all system data',
                'edit-daily-transactions' => 'Ability to edit daily transactions',
                'edit-all-daily-transactions' => 'Ability to edit all daily transactions across branches',
                'edit-own-branch-transactions' => 'Ability to edit transactions within own branch',
            ],
            'branch_management' => [
                'manage-branches' => 'Ability to manage all branches',
                'view-branches' => 'Ability to view branch information',
                'view-all-branches-data' => 'Ability to view data from all branches',
                'view-own-branch-data' => 'Ability to view data from own branch only',
            ],
            'line_management' => [
                'manage-sim-lines' => 'Ability to manage SIM card lines',
                'view-lines' => 'Ability to view line information',
            ],
            'safe_management' => [
                'manage-safes' => 'Ability to manage all safes',
                'manage-own-branch-safes' => 'Ability to manage safes within own branch',
            ],
            'customer_management' => [
                'view-customers' => 'Ability to view customer information',
                'manage-customers' => 'Ability to manage customer accounts',
            ],
            'reporting' => [
                'view-all-reports' => 'Ability to view all system reports',
                'view-client-reports' => 'Ability to view client reports',
                'view-audit-log' => 'Ability to view the audit log',
                'view-pending-transactions' => 'Ability to view pending transactions',
                'search-transactions' => 'Ability to search through transactions',
                'print-receipts' => 'Ability to print transaction receipts',
            ],
        ];

        // Create or update permissions with groups and descriptions
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permissionName => $description) {
                Permission::updateOrCreate(
                    ['name' => $permissionName],
                    [
                        'group' => $group,
                        'description' => $description,
                    ]
                );
            }
        }

        // Define roles with their associated permissions
        $roles = [
            'admin' => [
                'description' => 'System Administrator with full access',
                'permissions' => [
                    'create-users',
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
                    'manage-customers',
                ],
            ],
            'general_supervisor' => [
                'description' => 'General Supervisor with cross-branch oversight',
                'permissions' => [
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
                    'manage-customers',
                ],
            ],
            'auditor' => [
                'description' => 'Auditor with transaction review capabilities',
                'permissions' => [
                    'approve-pending-transactions',
                    'edit-all-daily-transactions',
                    'view-audit-log',
                    'view-customers',
                    'manage-customers',
                ],
            ],
            'branch_manager' => [
                'description' => 'Branch Manager with branch-level oversight',
                'permissions' => [
                    'view-own-branch-data',
                    'approve-own-branch-transactions',
                    'edit-own-branch-transactions',
                    'view-client-reports',
                    'view-pending-transactions',
                    'manage-own-branch-safes',
                    'approve-pending-transactions',
                    'view-customers',
                    'manage-customers',
                ],
            ],
            'agent' => [
                'description' => 'Agent with transaction processing capabilities',
                'permissions' => [
                    'send-transfer',
                    'receive-transfer',
                    'search-transactions',
                    'deposit-cash',
                    'print-receipts',
                    'view-lines',
                    'manage-customers',
                ],
            ],
            'trainee' => [
                'description' => 'Trainee with limited transaction capabilities',
                'permissions' => [
                    'send-transfer-pending',
                    'receive-transfer-pending',
                    'view-lines',
                ],
            ],
        ];

        // Create or update roles and assign permissions
        foreach ($roles as $roleName => $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleName],
                ['description' => $roleData['description'] ?? null]
            );

            $role->syncPermissions($roleData['permissions']);
        }
    }
}
