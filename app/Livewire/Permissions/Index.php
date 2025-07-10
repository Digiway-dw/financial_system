<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Index extends Component
{
    public function render()
    {
        // Get all permissions
        $permissions = Permission::orderBy('name')->get();

        // Group permissions by their group, handling the case where group may be null
        $permissionsByGroup = $permissions->groupBy(function ($permission) {
            return $permission->group ?? 'ungrouped';
        })->sortKeys();

        // Ensure all permissions have description to avoid null access errors
        foreach ($permissionsByGroup as $group => $groupPermissions) {
            foreach ($groupPermissions as $permission) {
                if (!isset($permission->description)) {
                    $permission->description = '';
                }
            }
        }

        // Get all roles with their permissions
        $roles = Role::with('permissions')->get();

        // Ensure all roles have description to avoid null access errors
        foreach ($roles as $role) {
            if (!isset($role->description)) {
                $role->description = '';
            }
        }

        return view('livewire.permissions.index', [
            'permissionsByGroup' => $permissionsByGroup,
            'roles' => $roles
        ]);
    }
}
