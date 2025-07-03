<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Entities\User;
use Spatie\Permission\Models\Role;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Cache;

class Index extends Component
{
    public $users;
    public $roles;
    public $editingUserId = null;
    public $selectedRole = null;

    public function mount()
    {
        Gate::authorize('manage-users');
        $this->loadUsers();
        $this->roles = Role::all();
    }

    #[On('user-role-updated')]
    public function loadUsers()
    {
        $this->users = User::all();
    }

    public function editRole(int $userId)
    {
        $this->editingUserId = $userId;
        $user = User::find($userId);
        $this->selectedRole = $user->getRoleNames()->first();
    }

    public function saveRole(int $userId)
    {
        Gate::authorize('manage-users');
        $user = User::find($userId);
        if ($user && $this->selectedRole) {
            $user->syncRoles([$this->selectedRole]);
            Cache::forget('spatie.permission.cache'); // Clear permission cache
            session()->flash('message', 'User role updated successfully.');
            $this->loadUsers();
            $this->cancelEdit();
        }
    }

    public function cancelEdit()
    {
        $this->editingUserId = null;
        $this->selectedRole = null;
    }

    public function render()
    {
        return view('livewire.users.index');
    }
}
