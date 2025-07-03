<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Entities\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class Create extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $selectedRole;
    public $roles = [];

    private UserRepository $userRepository;

    public function boot(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function mount()
    {
        $this->roles = Role::pluck('name')->toArray();
        $this->selectedRole = 'agent'; // Default role
    }

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'password' => 'required|string|min:8|confirmed',
            'selectedRole' => 'required|string|exists:roles,name',
        ];
    }

    public function createUser()
    {
        $this->validate();

        $user = $this->userRepository->create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        $user->assignRole($this->selectedRole);

        session()->flash('message', 'User created successfully.');

        return redirect()->route('users.index');
    }

    public function render()
    {
        return view('livewire.users.create');
    }
} 