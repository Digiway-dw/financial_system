<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Define additional permissions with groups and descriptions
        $additionalPermissions = [
            // Financial Operations - More granular control
            'financial_operations' => [
                'withdraw-with-limit' => 'Ability to perform withdrawals with daily/monthly limits',
                'deposit-cash-with-approval' => 'Ability to deposit cash requiring approval',
                'transfer-between-branches' => 'Ability to transfer funds between branches',
                'transfer-to-external-accounts' => 'Ability to transfer funds to external accounts',
                'handle-foreign-currency' => 'Ability to handle foreign currency transactions',
                'manage-recurring-transactions' => 'Ability to set up and manage recurring transactions',
            ],

            // Customer Management - Enhanced permissions
            'customer_management' => [
                'view-customer-history' => 'Ability to view customer transaction history',
                'edit-customer-details' => 'Ability to edit customer personal details',
                'manage-customer-accounts' => 'Ability to manage customer account settings',
                'blacklist-customers' => 'Ability to add customers to blacklist',
                'set-customer-limits' => 'Ability to set transaction limits for customers',
                'verify-customer-identity' => 'Ability to verify customer identity documents',
            ],

            // Reporting - Enhanced analytics
            'reporting' => [
                'generate-financial-reports' => 'Ability to generate financial reports',
                'export-transaction-data' => 'Ability to export transaction data',
                'view-system-analytics' => 'Ability to view system analytics dashboard',
                'schedule-automated-reports' => 'Ability to schedule automated reports',
                'view-suspicious-activity' => 'Ability to view flagged suspicious activity',
            ],

            // System Configuration
            'system_configuration' => [
                'manage-system-settings' => 'Ability to manage system-wide settings',
                'configure-notification-rules' => 'Ability to configure notification rules',
                'manage-exchange-rates' => 'Ability to update and manage exchange rates',
                'configure-security-settings' => 'Ability to configure security settings',
                'manage-api-integrations' => 'Ability to manage third-party API integrations',
            ],

            // Audit and Compliance
            'audit_compliance' => [
                'view-compliance-reports' => 'Ability to view compliance reports',
                'manage-kyc-requirements' => 'Ability to manage KYC requirements',
                'override-compliance-checks' => 'Ability to override compliance checks (with logging)',
                'manage-regulatory-reporting' => 'Ability to manage regulatory reporting',
                'view-audit-trails' => 'Ability to view detailed audit trails',
            ],

            // Communication
            'communication' => [
                'send-customer-notifications' => 'Ability to send notifications to customers',
                'manage-notification-templates' => 'Ability to manage notification templates',
                'send-bulk-communications' => 'Ability to send bulk communications',
                'view-communication-logs' => 'Ability to view communication logs',
            ],

            // Branch-specific enhancements
            'branch_management' => [
                'open-close-branches' => 'Ability to open or close branches',
                'set-branch-operating-hours' => 'Ability to set branch operating hours',
                'assign-staff-to-branches' => 'Ability to assign staff to branches',
                'view-branch-performance' => 'Ability to view branch performance metrics',
            ],

            // Staff management
            'staff_management' => [
                'manage-staff-schedules' => 'Ability to manage staff work schedules',
                'assign-staff-roles' => 'Ability to assign roles to staff members',
                'view-staff-activity' => 'Ability to view staff activity logs',
                'manage-staff-permissions' => 'Ability to manage individual staff permissions',
                'evaluate-staff-performance' => 'Ability to evaluate and record staff performance',
            ],

            // Transaction management
            'transaction_management' => [
                'void-transactions' => 'Ability to void transactions',
                'flag-suspicious-transactions' => 'Ability to flag suspicious transactions',
                'bulk-process-transactions' => 'Ability to process transactions in bulk',
                'schedule-future-transactions' => 'Ability to schedule future transactions',
                'manage-transaction-fees' => 'Ability to manage transaction fees',
            ],
        ];

        // Create permissions
        foreach ($additionalPermissions as $group => $permissions) {
            foreach ($permissions as $name => $description) {
                Permission::firstOrCreate(
                    ['name' => $name],
                    [
                        'group' => $group,
                        'description' => $description,
                    ]
                );
            }
        }

        // Assign new permissions to admin role
        $adminRole = Role::where('name', 'admin')->first();
        if ($adminRole) {
            $allNewPermissions = [];
            foreach ($additionalPermissions as $permissions) {
                $allNewPermissions = array_merge($allNewPermissions, array_keys($permissions));
            }
            $adminRole->givePermissionTo($allNewPermissions);
        }

        // Assign specific new permissions to other roles
        $this->assignPermissionsToRoles($additionalPermissions);
    }

    /**
     * Assign new permissions to existing roles
     */
    private function assignPermissionsToRoles($additionalPermissions)
    {
        // General Supervisor role
        $generalSupervisorRole = Role::where('name', 'general_supervisor')->first();
        if ($generalSupervisorRole) {
            $generalSupervisorRole->givePermissionTo([
                'view-customer-history',
                'edit-customer-details',
                'manage-customer-accounts',
                'set-customer-limits',
                'generate-financial-reports',
                'export-transaction-data',
                'view-system-analytics',
                'view-suspicious-activity',
                'view-compliance-reports',
                'view-audit-trails',
                'view-branch-performance',
                'view-staff-activity',
                'flag-suspicious-transactions',
            ]);
        }

        // Auditor role
        $auditorRole = Role::where('name', 'auditor')->first();
        if ($auditorRole) {
            $auditorRole->givePermissionTo([
                'view-customer-history',
                'generate-financial-reports',
                'export-transaction-data',
                'view-suspicious-activity',
                'view-compliance-reports',
                'view-audit-trails',
                'flag-suspicious-transactions',
            ]);
        }

        // Branch Manager role
        $branchManagerRole = Role::where('name', 'branch_manager')->first();
        if ($branchManagerRole) {
            $branchManagerRole->givePermissionTo([
                'withdraw-with-limit',
                'deposit-cash-with-approval',
                'view-customer-history',
                'edit-customer-details',
                'manage-customer-accounts',
                'set-customer-limits',
                'verify-customer-identity',
                'generate-financial-reports',
                'view-branch-performance',
                'manage-staff-schedules',
                'view-staff-activity',
                'send-customer-notifications',
                'view-communication-logs',
                'void-transactions',
                'flag-suspicious-transactions',
            ]);
        }

        // Agent role
        $agentRole = Role::where('name', 'agent')->first();
        if ($agentRole) {
            $agentRole->givePermissionTo([
                'withdraw-with-limit',
                'deposit-cash-with-approval',
                'view-customer-history',
                'verify-customer-identity',
                'send-customer-notifications',
            ]);
        }

        // Trainee role remains limited
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Define the permissions to be removed
        $permissionsToRemove = [
            'withdraw-with-limit',
            'deposit-cash-with-approval',
            'transfer-between-branches',
            'transfer-to-external-accounts',
            'handle-foreign-currency',
            'manage-recurring-transactions',
            'view-customer-history',
            'edit-customer-details',
            'manage-customer-accounts',
            'blacklist-customers',
            'set-customer-limits',
            'verify-customer-identity',
            'generate-financial-reports',
            'export-transaction-data',
            'view-system-analytics',
            'schedule-automated-reports',
            'view-suspicious-activity',
            'manage-system-settings',
            'configure-notification-rules',
            'manage-exchange-rates',
            'configure-security-settings',
            'manage-api-integrations',
            'view-compliance-reports',
            'manage-kyc-requirements',
            'override-compliance-checks',
            'manage-regulatory-reporting',
            'view-audit-trails',
            'send-customer-notifications',
            'manage-notification-templates',
            'send-bulk-communications',
            'view-communication-logs',
            'open-close-branches',
            'set-branch-operating-hours',
            'assign-staff-to-branches',
            'view-branch-performance',
            'manage-staff-schedules',
            'assign-staff-roles',
            'view-staff-activity',
            'manage-staff-permissions',
            'evaluate-staff-performance',
            'void-transactions',
            'flag-suspicious-transactions',
            'bulk-process-transactions',
            'schedule-future-transactions',
            'manage-transaction-fees',
        ];

        // Remove permissions
        foreach ($permissionsToRemove as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }
    }
};
