<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add group column to permissions table
        Schema::table('permissions', function (Blueprint $table) {
            $table->string('group')->nullable()->after('name');
            $table->text('description')->nullable()->after('group');
        });

        // Define permission groups
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

        // Update existing permissions with groups and descriptions
        foreach ($permissionGroups as $group => $permissions) {
            foreach ($permissions as $permissionName => $description) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $permission->group = $group;
                    $permission->description = $description;
                    $permission->save();
                } else {
                    // Create permission if it doesn't exist
                    Permission::create([
                        'name' => $permissionName,
                        'group' => $group,
                        'description' => $description,
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove permissions that were created in this migration
        $newPermissions = [
            // Add any new permissions created in this migration
        ];

        foreach ($newPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $permission->delete();
            }
        }

        // Remove group and description columns
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('group');
            $table->dropColumn('description');
        });
    }
};
