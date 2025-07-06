<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\User;

class DeleteUserForm extends Component
{
    public $password;
    public $confirmingUserDeletion = false;

    public function confirmUserDeletion()
    {
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        $this->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = User::find(Auth::id());

        Auth::logout();

        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.profile.delete-user-form');
    }
}
