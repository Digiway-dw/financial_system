<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Entities\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;
use App\Models\Domain\Entities\Branch;
use Illuminate\Support\Facades\Hash;

class Index extends Component
{
    use WithPagination;

    public $roles;
    public $branches;
    public $editingUserId = null;
    public $selectedRole = null;
    public $name = '';
    public $role = '';
    public $branchId = '';
    public $showTrashed = false;
    public $confirmingUserDeletion = false;
    public $confirmingUserRestore = false;
    public $userBeingDeleted = null;
    public $userBeingRestored = null;

    public $sortField = 'name';
    public $sortDirection = 'asc';

    protected $queryString = [
        'name' => ['except' => ''],
        'role' => ['except' => ''],
        'branchId' => ['except' => ''],
        'showTrashed' => ['except' => false],
    ];

    public function mount()
    {
        // Check if current user can manage users
        if (!$this->canManageUsers()) {
            abort(403, 'You are not authorized to manage users.');
        }

        // Create admin role if it doesn't exist
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin']);

            // Add all permissions to admin role
            $permissions = \Spatie\Permission\Models\Permission::all();
            $adminRole->syncPermissions($permissions);
        }

        // Load available roles based on current user's permissions
        $this->roles = $this->getAvailableRoles();
        $this->branches = Branch::orderBy('name')->get();
    }

    public function canManageUsers(): bool
    {
        $currentUser = auth()->user();
        return $currentUser && $currentUser->hasRole(['admin', 'general_supervisor']);
    }

    public function canEditUserRole($targetUser): bool
    {
        $currentUser = auth()->user();
        
        // Admin can edit anyone except other admins
        if ($currentUser->hasRole('admin')) {
            return !$targetUser->hasRole('admin') || $currentUser->id === $targetUser->id;
        }
        
        // Supervisor can edit non-admin, non-supervisor users
        if ($currentUser->hasRole('general_supervisor')) {
            return !$targetUser->hasRole(['admin', 'general_supervisor']);
        }
        
        // Other users cannot edit anyone
        return false;
    }

    public function canViewUser($targetUser): bool
    {
        $currentUser = auth()->user();
        
        // Admin can view anyone except other admins
        if ($currentUser->hasRole('admin')) {
            return !$targetUser->hasRole('admin') || $currentUser->id === $targetUser->id;
        }
        
        // Supervisor can view non-admin, non-supervisor users
        if ($currentUser->hasRole('general_supervisor')) {
            return !$targetUser->hasRole(['admin', 'general_supervisor']);
        }
        
        // Other users cannot view anyone
        return false;
    }

    public function getAvailableRoles()
    {
        $currentUser = auth()->user();
        $allRoles = Role::all();
        
        // Admin can assign all roles except admin to others
        if ($currentUser->hasRole('admin')) {
            return $allRoles->filter(function($role) {
                return $role->name !== 'admin';
            });
        }
        
        // Supervisor can only assign these roles
        if ($currentUser->hasRole('general_supervisor')) {
            $allowedRoles = ['agent', 'branch_manager', 'trainee', 'auditor'];
            return $allRoles->filter(function($role) use ($allowedRoles) {
                return in_array($role->name, $allowedRoles);
            });
        }
        
        // Other users cannot assign roles
        return collect([]);
    }

    public function filter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['name', 'role', 'branchId', 'showTrashed']);
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    #[On('user-role-updated')]
    public function refreshUsers()
    {
        // Just refresh the component
    }

    public function editRole(int $userId)
    {
        $user = User::find($userId);
        
        // Check if current user can edit this user's role
        if (!$this->canEditUserRole($user)) {
            session()->flash('error', 'You are not authorized to edit this user\'s role.');
            return;
        }
        
        $this->editingUserId = $userId;
        $this->selectedRole = $user->getRoleNames()->first();
    }

    public function saveRole()
    {
        $user = User::find($this->editingUserId);
        
        // Check if current user can edit this user's role
        if (!$user || !$this->canEditUserRole($user)) {
            session()->flash('error', 'You are not authorized to edit this user\'s role.');
            $this->cancelEdit();
            return;
        }
        
        // Validate that the selected role is allowed for current user
        $availableRoles = $this->getAvailableRoles()->pluck('name')->toArray();
        if (!in_array($this->selectedRole, $availableRoles)) {
            session()->flash('error', 'You are not authorized to assign this role.');
            $this->cancelEdit();
            return;
        }
        
        if ($this->selectedRole) {
            $user->syncRoles([$this->selectedRole]);
            Cache::forget('spatie.permission.cache'); // Clear permission cache
            session()->flash('message', 'User role updated successfully.');
            $this->cancelEdit();
        }
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
        $this->selectedRole = null;
    }

    public function confirmUserDeletion($userId)
    {
        $this->userBeingDeleted = User::find($userId);
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        if ($this->userBeingDeleted) {
            Gate::authorize('delete', $this->userBeingDeleted);
            $this->userBeingDeleted->delete();
            session()->flash('message', 'User deleted successfully.');
            $this->confirmingUserDeletion = false;
            $this->userBeingDeleted = null;
        }
    }

    public function confirmRestore($userId)
    {
        $this->userBeingRestored = User::withTrashed()->find($userId);
        $this->confirmingUserRestore = true;
    }

    public function restoreUser()
    {
        if ($this->userBeingRestored) {
            Gate::authorize('restore', $this->userBeingRestored);
            $this->userBeingRestored->restore();
            session()->flash('message', 'User restored successfully.');
            $this->confirmingUserRestore = false;
            $this->userBeingRestored = null;
        }
    }

    public function canToggleIgnoreWorkHours($targetUser): bool
    {
        $currentUser = auth()->user();
        // Only admin and supervisor can toggle
        if (!$currentUser || (!$currentUser->hasRole('admin') && !$currentUser->hasRole('general_supervisor'))) {
            return false;
        }
        // Supervisor cannot toggle for admin
        if ($currentUser->hasRole('general_supervisor') && $targetUser->hasRole('admin')) {
            return false;
        }
        // Only admin can toggle for themselves
        if ($targetUser->hasRole('admin') && $currentUser->id !== $targetUser->id) {
            return false;
        }
        return true;
    }

    public function toggleIgnoreWorkHours($userId)
    {
        $user = User::findOrFail($userId);
        if (!$this->canToggleIgnoreWorkHours($user)) {
            abort(403, 'You are not authorized to edit this user.');
        }
        $user->ignore_work_hours = !$user->ignore_work_hours;
        $user->save();
        session()->flash('message', 'تم تحديث حالة تجاهل أوقات العمل للمستخدم بنجاح.');
    }

    public function render()
    {
        $query = User::with('branch');

        if ($this->showTrashed) {
            $query->withTrashed();
        }

        if ($this->name) {
            $query->where('name', 'like', '%' . $this->name . '%');
        }

        if ($this->role) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', $this->role);
            });
        }

        if ($this->branchId) {
            $query->where('branch_id', $this->branchId);
        }

        // Apply sorting
        if ($this->sortField === 'role') {
            // Handle role sorting by joining with the roles table
            $query->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                  ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id')
                  ->orderBy('roles.name', $this->sortDirection)
                  ->orderBy('users.name', 'asc') // Secondary sort to ensure consistent results
                  ->select('users.*');
        } else {
            // Regular sorting for users table columns
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $users = $query->paginate(10);

        return view('livewire.users.index', [
            'users' => $users,
            'branches' => $this->branches
        ]);
    }
}
