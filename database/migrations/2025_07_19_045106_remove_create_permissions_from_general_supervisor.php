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
            // Remove create permissions from general supervisor
            $generalSupervisorRole->revokePermissionTo([
                'create-safes',
                'create-branches'
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
            // Add back the create permissions to general supervisor
            $generalSupervisorRole->givePermissionTo([
                'create-safes',
                'create-branches'
            ]);
        }
    }
};
