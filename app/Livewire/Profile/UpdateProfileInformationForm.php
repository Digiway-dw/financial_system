<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Entities\User;

class UpdateProfileInformationForm extends Component
{
    public $name;
    public $email;
    public $phone_number;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
    }

    public function updateProfileInformation()
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . Auth::id()],
            'phone_number' => ['nullable', 'string', 'max:20'],
        ]);

        $user = User::find(Auth::id());
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone_number = $this->phone_number;
        $user->save();

        session()->flash('status', 'Profile updated successfully.');
    }

    public function render()
    {
        return view('livewire.profile.update-profile-information-form');
    }
}
