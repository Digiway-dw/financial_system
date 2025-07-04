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
    public $branchId;
    public $branches = [];
    public $phone_number;
    public $national_number;
    public $salary;
    public $address;
    public $land_number;
    public $relative_phone_number;
    public $notes;

    private UserRepository $userRepository;

    public function boot(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function mount()
    {
        $this->roles = Role::pluck('name')->toArray();
        $this->selectedRole = 'agent'; // Default role
        $this->branches = \App\Models\Domain\Entities\Branch::all();
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
            'branchId' => [
                function ($attribute, $value, $fail) {
                    if (!in_array($this->selectedRole, ['admin']) && empty($value)) {
                        $fail('Branch is required for this role.');
                    }
                },
                'nullable',
                'exists:branches,id',
            ],
            'phone_number' => 'nullable|string|max:20',
            'national_number' => 'nullable|digits:14',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'land_number' => 'nullable|string|max:20',
            'relative_phone_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
        ];
    }

    public function createUser()
    {
        $this->validate();

        $userData = [
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'phone_number' => $this->phone_number,
            'national_number' => $this->national_number,
            'salary' => $this->salary,
            'address' => $this->address,
            'land_number' => $this->land_number,
            'relative_phone_number' => $this->relative_phone_number,
            'notes' => $this->notes,
        ];
        if (!in_array($this->selectedRole, ['admin'])) {
            $userData['branch_id'] = $this->branchId;
        }

        $user = $this->userRepository->create($userData);
        $user->assignRole($this->selectedRole);

        session()->flash('message', 'User created successfully.');
        $this->redirect(route('users.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.users.create', [
            'branches' => $this->branches,
        ]);
    }
} 