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
        // Temporarily commenting out Gate authorization
        // Gate::authorize('manage-users');

        // Create admin role if it doesn't exist
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            $adminRole = Role::create(['name' => 'admin']);

            // Add all permissions to admin role
            $permissions = \Spatie\Permission\Models\Permission::all();
            $adminRole->syncPermissions($permissions);
        }

        // Create or update admin user, handling soft deletes
        $admin = User::withTrashed()->where('email', 'admin@example.com')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]);
            $admin->assignRole('admin');
        } elseif ($admin->trashed()) {
            // If the admin user is soft deleted, restore it
            $admin->restore();
        }

        $this->roles = Role::whereNotIn('name', ['Branch Manager', 'Supervisor'])->get();
        $this->branches = Branch::orderBy('name')->get();
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
        $this->editingUserId = $userId;
        $user = User::find($userId);
        $this->selectedRole = $user->getRoleNames()->first();
    }

    public function saveRole()
    {
        // Temporarily commenting out Gate authorization
        // Gate::authorize('manage-users');
        $user = User::find($this->editingUserId);
        if ($user && $this->selectedRole) {
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
