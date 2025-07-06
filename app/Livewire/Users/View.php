<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use Spatie\Permission\Models\Role;

class View extends Component
{
    public $user;
    public $branch;
    public $role;
    public $transactions;
    public $loginHistories;

    public function mount($userId)
    {
        $user = User::findOrFail($userId);
        // if ($user->hasRole('admin')) {
        //     abort(403, 'Cannot view admin user.');
        // }
        $this->user = $user;
        $this->branch = $user->branch;
        $this->role = $user->getRoleNames()->first();
        $this->transactions = $user->transactions()->latest()->get();
        $this->loginHistories = $user->loginHistories()->orderByDesc('login_at')->get();
    }

    public function render()
    {
        return view('livewire.users.view', [
            'user' => $this->user,
            'branch' => $this->branch,
            'role' => $this->role,
            'transactions' => $this->transactions,
            'loginHistories' => $this->loginHistories,
        ]);
    }
}
