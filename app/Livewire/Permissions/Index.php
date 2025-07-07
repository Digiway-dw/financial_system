<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    public function render()
    {
        // Get all permissions grouped by their group
        $permissionsByGroup = Permission::orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');

        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();

        return view('livewire.permissions.index', [
            'permissionsByGroup' => $permissionsByGroup,
            'roles' => $roles
        ]);
    }
}
