<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class Edit extends Component
{
    public $userId;
    public $name;
    public $email;
    public $phone_number;
    public $national_number;
    public $salary;
    public $address;
    public $land_number;
    public $relative_phone_number;
    public $notes;
    public $branchId;
    public $selectedRole;
    public $roles = [];
    public $branches = [];

    public function mount($userId)
    {
        // Temporarily commenting out the role check
        // if (!auth()->user() || !auth()->user()->hasRole('admin')) {
        //     abort(403, 'Only admin can edit user details.');
        // }
        $user = User::findOrFail($userId);
        // if ($user->hasRole('admin')) {
        //     abort(403, 'Cannot edit admin user.');
        // }
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->national_number = $user->national_number;
        $this->salary = $user->salary;
        $this->address = $user->address;
        $this->land_number = $user->land_number;
        $this->relative_phone_number = $user->relative_phone_number;
        $this->notes = $user->notes;
        $this->branchId = $user->branch_id;
        $this->selectedRole = $user->getRoleNames()->first();
        $this->roles = Role::pluck('name')->toArray();
        $this->branches = Branch::all();
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
                Rule::unique(User::class)->ignore($this->userId),
            ],
            'phone_number' => 'nullable|string|max:20',
            'national_number' => 'nullable|digits:14',
            'salary' => 'nullable|numeric|min:0',
            'address' => 'nullable|string|max:255',
            'land_number' => 'nullable|string|max:20',
            'relative_phone_number' => 'nullable|string|max:20',
            'notes' => 'nullable|string',
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
        ];
    }

    public function updateUser()
    {
        $this->validate();
        $user = User::findOrFail($this->userId);
        // if ($user->hasRole('admin')) {
        //     abort(403, 'Cannot edit admin user.');
        // }
        $user->name = $this->name;
        $user->email = $this->email;
        $user->phone_number = $this->phone_number;
        $user->national_number = $this->national_number;
        $user->salary = $this->salary;
        $user->address = $this->address;
        $user->land_number = $this->land_number;
        $user->relative_phone_number = $this->relative_phone_number;
        $user->notes = $this->notes;
        $user->branch_id = !in_array($this->selectedRole, ['admin']) ? $this->branchId : null;
        $user->save();
        $user->syncRoles([$this->selectedRole]);
        session()->flash('message', 'User updated successfully.');
        return redirect()->route('users.view', ['userId' => $user->id]);
    }

    public function render()
    {
        return view('livewire.users.edit', [
            'branches' => $this->branches,
            'roles' => $this->roles,
        ]);
    }
}
