<?php

namespace App\Livewire\Permissions;

use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;

class RolePermissions extends Component
{
    use WithPagination;

    public $selectedRole = null;
    public $roles = [];
    public $permissionsByGroup = [];
    public $selectedPermissions = [];
    public $searchTerm = '';
    public $successMessage = '';
    public $errorMessage = '';
    public $selectedGroup = 'all';

    protected $queryString = ['selectedRole', 'selectedGroup', 'searchTerm'];

    public function mount()
    {
        $this->roles = Role::orderBy('name')->get();

        if ($this->selectedRole) {
            $this->loadRolePermissions();
        } else if (count($this->roles) > 0) {
            $this->selectedRole = $this->roles->first()->id;
            $this->loadRolePermissions();
        }

        $this->loadPermissionGroups();
    }

    public function loadRolePermissions()
    {
        if (!$this->selectedRole) {
            return;
        }

        $role = Role::findById($this->selectedRole);
        $this->selectedPermissions = $role->permissions->pluck('id')->toArray();
    }

    public function loadPermissionGroups()
    {
        $query = Permission::query();

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('description', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if ($this->selectedGroup && $this->selectedGroup !== 'all') {
            $query->where('group', $this->selectedGroup);
        }

        $permissions = $query->orderBy('group')->orderBy('name')->get();
        $this->permissionsByGroup = $permissions->groupBy('group');
    }

    public function updatedSearchTerm()
    {
        $this->loadPermissionGroups();
    }

    public function updatedSelectedGroup()
    {
        $this->loadPermissionGroups();
    }

    public function updatedSelectedRole()
    {
        $this->loadRolePermissions();
    }

    public function togglePermission($permissionId)
    {
        if (in_array($permissionId, $this->selectedPermissions)) {
            $this->selectedPermissions = array_diff($this->selectedPermissions, [$permissionId]);
        } else {
            $this->selectedPermissions[] = $permissionId;
        }
    }

    public function toggleGroupPermissions($group, $checked)
    {
        if (!isset($this->permissionsByGroup[$group])) {
            return;
        }

        $groupPermissionIds = $this->permissionsByGroup[$group]->pluck('id')->toArray();

        if ($checked) {
            $this->selectedPermissions = array_unique(array_merge($this->selectedPermissions, $groupPermissionIds));
        } else {
            $this->selectedPermissions = array_diff($this->selectedPermissions, $groupPermissionIds);
        }
    }

    public function savePermissions()
    {
        try {
            DB::beginTransaction();

            $role = Role::findById($this->selectedRole);
            $permissions = Permission::whereIn('id', $this->selectedPermissions)->get();

            $role->syncPermissions($permissions);

            DB::commit();

            $this->successMessage = "Permissions for role '{$role->name}' updated successfully.";
            $this->errorMessage = '';

            $this->dispatch('permissions-updated');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = "Error updating permissions: " . $e->getMessage();
            $this->successMessage = '';
        }
    }

    public function clearMessages()
    {
        $this->successMessage = '';
        $this->errorMessage = '';
    }

    public function getAvailableGroupsProperty()
    {
        return Permission::select('group')
            ->distinct()
            ->orderBy('group')
            ->pluck('group')
            ->filter()
            ->toArray();
    }

    public function isGroupSelected($group)
    {
        if (!isset($this->permissionsByGroup[$group])) {
            return false;
        }

        $groupPermissionIds = $this->permissionsByGroup[$group]->pluck('id')->toArray();
        $intersection = array_intersect($groupPermissionIds, $this->selectedPermissions);

        return count($intersection) === count($groupPermissionIds);
    }

    public function isGroupPartiallySelected($group)
    {
        if (!isset($this->permissionsByGroup[$group])) {
            return false;
        }

        $groupPermissionIds = $this->permissionsByGroup[$group]->pluck('id')->toArray();
        $intersection = array_intersect($groupPermissionIds, $this->selectedPermissions);

        return count($intersection) > 0 && count($intersection) < count($groupPermissionIds);
    }

    public function render()
    {
        return view('livewire.permissions.role-permissions', [
            'availableGroups' => $this->availableGroups,
        ]);
    }
}
