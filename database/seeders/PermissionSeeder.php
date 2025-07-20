<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Agent Dashboard permissions
        Permission::create(['name' => 'view-agent-dashboard']);
        
        // Assign permission to admin and supervisor roles
        $adminRole = Role::where('name', 'admin')->first();
        $supervisorRole = Role::where('name', 'general_supervisor')->first();
        
        if ($adminRole) {
            $adminRole->givePermissionTo('view-agent-dashboard');
        }
        
        if ($supervisorRole) {
            $supervisorRole->givePermissionTo('view-agent-dashboard');
        }
    }
} 