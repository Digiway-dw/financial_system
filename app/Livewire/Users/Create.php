<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Entities\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
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

    // Working hours properties
    public $workingHours = [];
    public $days = [
        'every_day' => 'Every day',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];
    public $dayOfWeek = null;
    public $startTime = '09:00';
    public $endTime = '17:00';
    public $isEnabled = true;
    public $tempWorkingHours = [];
    public $deleteConfirmIndex = null;

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
                    if (!in_array($this->selectedRole, ['admin', 'general_supervisor', 'auditor']) && empty($value)) {
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
            // Working hours validation rules
            'dayOfWeek' => 'nullable|in:every_day,monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'startTime' => 'nullable|date_format:H:i',
            'endTime' => 'nullable|date_format:H:i|after:startTime',
            'isEnabled' => 'boolean',
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

        // Create working hours for the user
        foreach ($this->tempWorkingHours as $workingHour) {
            $newWorkingHour = new WorkingHour();
            $newWorkingHour->user_id = $user->id;
            $newWorkingHour->day_of_week = $workingHour['day_of_week'];
            $newWorkingHour->start_time = $workingHour['start_time'];
            $newWorkingHour->end_time = $workingHour['end_time'];
            $newWorkingHour->is_enabled = $workingHour['is_enabled'];
            $newWorkingHour->save();
        }

        session()->flash('message', 'User created successfully.');
        $this->redirect(route('users.index'), navigate: true);
    }

    // Working hours methods
    public function resetWorkingHourForm()
    {
        $this->dayOfWeek = null;
        $this->startTime = '09:00';
        $this->endTime = '17:00';
        $this->isEnabled = true;
        $this->resetValidation(['dayOfWeek', 'startTime', 'endTime']);
    }

    public function addWorkingHour()
    {
        $this->validate([
            'dayOfWeek' => 'required|in:every_day,monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'isEnabled' => 'boolean',
        ]);

        // Handle "every_day" option
        if ($this->dayOfWeek === 'every_day') {
            $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

            foreach ($weekdays as $day) {
                // Check if day already exists in temp working hours
                $existingIndex = null;
                foreach ($this->tempWorkingHours as $index => $workingHour) {
                    if ($workingHour['day_of_week'] === $day) {
                        $existingIndex = $index;
                        break;
                    }
                }

                if ($existingIndex !== null) {
                    // Update existing entry
                    $this->tempWorkingHours[$existingIndex] = [
                        'day_of_week' => $day,
                        'start_time' => $this->startTime,
                        'end_time' => $this->endTime,
                        'is_enabled' => $this->isEnabled,
                    ];
                } else {
                    // Add new entry
                    $this->tempWorkingHours[] = [
                        'day_of_week' => $day,
                        'start_time' => $this->startTime,
                        'end_time' => $this->endTime,
                        'is_enabled' => $this->isEnabled,
                    ];
                }
            }

            session()->flash('workingHourMessage', 'Working hours added for all days successfully.');
            $this->resetWorkingHourForm();
            return;
        }

        // Regular single day handling
        // Check if day already exists in temp working hours
        $existingIndex = null;
        foreach ($this->tempWorkingHours as $index => $workingHour) {
            if ($workingHour['day_of_week'] === $this->dayOfWeek) {
                $existingIndex = $index;
                break;
            }
        }

        if ($existingIndex !== null) {
            // Update existing entry
            $this->tempWorkingHours[$existingIndex] = [
                'day_of_week' => $this->dayOfWeek,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'is_enabled' => $this->isEnabled,
            ];
            session()->flash('workingHourMessage', 'Working hours updated successfully.');
        } else {
            // Add new entry
            $this->tempWorkingHours[] = [
                'day_of_week' => $this->dayOfWeek,
                'start_time' => $this->startTime,
                'end_time' => $this->endTime,
                'is_enabled' => $this->isEnabled,
            ];
            session()->flash('workingHourMessage', 'Working hours added successfully.');
        }

        $this->resetWorkingHourForm();
    }

    public function removeWorkingHour($index)
    {
        if (isset($this->tempWorkingHours[$index])) {
            unset($this->tempWorkingHours[$index]);
            $this->tempWorkingHours = array_values($this->tempWorkingHours);
            session()->flash('workingHourMessage', 'Working hours removed successfully.');
        }
        
        // Close the confirmation dialog
        $this->deleteConfirmIndex = null;
    }

    public function editWorkingHour($index)
    {
        if (isset($this->tempWorkingHours[$index])) {
            $workingHour = $this->tempWorkingHours[$index];
            $this->dayOfWeek = $workingHour['day_of_week'];
            $this->startTime = $workingHour['start_time'];
            $this->endTime = $workingHour['end_time'];
            $this->isEnabled = $workingHour['is_enabled'];

            // Remove the entry (it will be re-added when saved)
            $this->removeWorkingHour($index);
        }
    }

    public function confirmRemove($index)
    {
        $this->deleteConfirmIndex = $index;
    }

    public function cancelRemove()
    {
        $this->deleteConfirmIndex = null;
    }

    public function confirmRemoveAction()
    {
        if ($this->deleteConfirmIndex !== null) {
            $this->removeWorkingHour($this->deleteConfirmIndex);
        }
    }

    public function toggleWorkingHourStatus($index)
    {
        if (isset($this->tempWorkingHours[$index])) {
            $this->tempWorkingHours[$index]['is_enabled'] = !$this->tempWorkingHours[$index]['is_enabled'];
            session()->flash('workingHourMessage', 'Working hour status toggled successfully.');
        }
    }

    public function render()
    {
        // Debug message to check if component is working
        session()->flash('message', 'Component loaded successfully. Working hours section should be visible.');

        return view('livewire.users.create', [
            'branches' => $this->branches,
        ]);
    }
}
