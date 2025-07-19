<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the general supervisor role
        $generalSupervisorRole = Role::where('name', 'general_supervisor')->first();
        
        if ($generalSupervisorRole) {
            // Remove edit and delete permissions from general supervisor
            $generalSupervisorRole->revokePermissionTo([
                'edit-safes',
                'delete-safes',
                'edit-branches',
                'delete-branches'
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Get the general supervisor role
        $generalSupervisorRole = Role::where('name', 'general_supervisor')->first();
        
        if ($generalSupervisorRole) {
            // Add back the edit and delete permissions to general supervisor
            $generalSupervisorRole->givePermissionTo([
                'edit-safes',
                'delete-safes',
                'edit-branches',
                'delete-branches'
            ]);
        }
    }
};
