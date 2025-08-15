<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use App\Models\WorkingHour;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Carbon\Carbon;

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
    public $ignore_work_hours = false;

    // Add password fields for admin password change
    public $password = '';
    public $password_confirmation = '';

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
    public $editingWorkingHourId = null;
    public $deleteConfirmId = null;
    public $dayOfWeek = null;
    public $startTime = '09:00';
    public $endTime = '17:00';
    public $isEnabled = true;

    public function mount($userId)
    {
        $user = User::findOrFail($userId);
        
        // Check if current user can edit this user
        if (!$this->canEditUser($user)) {
            abort(403, 'You are not authorized to edit this user.');
        }
        
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->national_number = $user->national_number;
        $this->salary = $user->salary ? (int) $user->salary : null; // Cast to int to remove .00
        $this->address = $user->address;
        $this->land_number = $user->land_number;
        $this->relative_phone_number = $user->relative_phone_number;
        $this->notes = $user->notes;
        $this->branchId = $user->branch_id;
        $this->selectedRole = $user->getRoleNames()->first();
        $this->ignore_work_hours = (bool) $user->ignore_work_hours;
        
        // Load available roles based on current user's permissions
        $this->roles = $this->getAvailableRoles();
        $this->branches = Branch::all();

        // Load working hours
        $this->loadWorkingHours();
    }

    public function canEditUser($targetUser): bool
    {
        $currentUser = auth()->user();
        
        // Admins can edit any user except other admins (unless it's their own account or admin@financial.system)
        if ($currentUser->hasRole('admin')) {
            if ($targetUser->hasRole('admin')) {
                return $currentUser->email === 'admin@financial.system' && $currentUser->id !== $targetUser->id || $currentUser->id === $targetUser->id;
            }
            return true;
        }

        // Supervisor can only edit their own profile
        if ($currentUser->hasRole('general_supervisor')) {
            return $currentUser->id === $targetUser->id;
        }

        // Other users cannot edit anyone
        return false;
    }

    public function canEditRoles(): bool
    {
        $currentUser = auth()->user();
        return $currentUser->hasRole(['admin', 'general_supervisor']);
    }

    public function getAvailableRoles(): array
    {
        $currentUser = auth()->user();
        $allRoles = Role::pluck('name')->toArray();
        
        // admin@financial.system can assign all roles including admin
        if ($currentUser->hasRole('admin')) {
            if ($currentUser->email === 'admin@financial.system') {
                return $allRoles;
            }
            // Other admins can assign all roles except admin
            return array_filter($allRoles, function($role) {
                return $role !== 'admin';
            });
        }
        
        // Supervisor can only assign these roles
        if ($currentUser->hasRole('general_supervisor')) {
            $allowedRoles = ['agent', 'branch_manager', 'trainee', 'auditor'];
            return array_filter($allRoles, function($role) use ($allowedRoles) {
                return in_array($role, $allowedRoles);
            });
        }
        
        // Other users cannot assign roles
        return [];
    }

    public function canEditIgnoreWorkHours($targetUser = null): bool
    {
        $currentUser = auth()->user();
        $targetUser = $targetUser ?: User::findOrFail($this->userId);
        // Only admin and supervisor can edit
        if (!$currentUser || (!$currentUser->hasRole('admin') && !$currentUser->hasRole('general_supervisor'))) {
            return false;
        }
        // Supervisor cannot edit for admin
        if ($currentUser->hasRole('general_supervisor') && $targetUser->hasRole('admin')) {
            return false;
        }
        // Only admin can edit for themselves
        if ($targetUser->hasRole('admin') && $currentUser->id !== $targetUser->id) {
            return false;
        }
        return true;
    }

    public function loadWorkingHours()
    {
        $this->workingHours = WorkingHour::where('user_id', $this->userId)
            ->orderBy('day_of_week')
            ->get();
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
                    if (!in_array($this->selectedRole, ['admin', 'general_supervisor', 'auditor']) && empty($value)) {
                        $fail('Branch is required for this role.');
                    }
                },
                'nullable',
                'exists:branches,id',
            ],
            'ignore_work_hours' => 'boolean',
            // Working hours validation rules
            'dayOfWeek' => 'nullable|in:every_day,monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'startTime' => 'nullable|date_format:H:i',
            'endTime' => 'nullable|date_format:H:i|after:startTime',
            'isEnabled' => 'boolean',
            // Password validation (optional, only if filled)
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ];
    }

    public function updateUser()
    {
        $user = User::findOrFail($this->userId);
        if (!$this->canEditUser($user)) {
            abort(403, 'You are not authorized to edit this user.');
        }
        // Prevent supervisor from editing admin
        if (auth()->user()->hasRole('general_supervisor') && $user->hasRole('admin')) {
            abort(403, 'You are not authorized to edit the admin user.');
        }
        $this->validate();

        // Check if role change is allowed
        if ($this->selectedRole !== $user->getRoleNames()->first()) {
            if (!$this->canEditRoles()) {
                abort(403, 'You are not authorized to change user roles.');
            }

            // Validate that the selected role is allowed for current user
            $availableRoles = $this->getAvailableRoles();
            if (!in_array($this->selectedRole, $availableRoles)) {
                abort(403, 'You are not authorized to assign this role.');
            }
        }

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
        $user->ignore_work_hours = (bool) $this->ignore_work_hours;
        // If password is filled, update it
        if (!empty($this->password)) {
            $user->password = bcrypt($this->password);
        }
        $user->save();

        // Only update role if user has permission and role has changed
        if ($this->canEditRoles() && $this->selectedRole !== $user->getRoleNames()->first()) {
            $user->syncRoles([$this->selectedRole]);
        }

        session()->flash('message', 'User updated successfully.');
        return redirect()->route('users.index');
    }

    // Working hours methods
    public function resetWorkingHourForm()
    {
        $this->editingWorkingHourId = null;
        $this->dayOfWeek = null;
        $this->startTime = '09:00';
        $this->endTime = '17:00';
        $this->isEnabled = true;
        $this->resetValidation(['dayOfWeek', 'startTime', 'endTime']);
    }

    public function editWorkingHour($id)
    {
        $workingHour = WorkingHour::findOrFail($id);
        $this->editingWorkingHourId = $id;
        $this->dayOfWeek = $workingHour->day_of_week;
        $this->startTime = Carbon::parse($workingHour->start_time)->format('H:i');
        $this->endTime = Carbon::parse($workingHour->end_time)->format('H:i');
        $this->isEnabled = $workingHour->is_enabled;
    }

    public function saveWorkingHour()
    {
        $this->validate([
            'dayOfWeek' => 'required|in:every_day,monday,tuesday,wednesday,thursday,friday,saturday,sunday',
            'startTime' => 'required|date_format:H:i',
            'endTime' => 'required|date_format:H:i|after:startTime',
            'isEnabled' => 'boolean',
        ]);

        try {
            // Handle "every_day" option
            if (
                $this->dayOfWeek === 'every_day'
            ) {
                $weekdays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                $dayMap = [
                    'sunday' => 0,
                    'monday' => 1,
                    'tuesday' => 2,
                    'wednesday' => 3,
                    'thursday' => 4,
                    'friday' => 5,
                    'saturday' => 6,
                ];
                foreach ($weekdays as $day) {
                    // Check for existing record for the same user and day
                    $existing = WorkingHour::where('user_id', $this->userId)
                        ->where('day_of_week', $day)
                        ->first();

                    if ($existing) {
                        // Update existing record
                        $existing->update([
                            'start_time' => $this->startTime,
                            'end_time' => $this->endTime,
                            'is_enabled' => $this->isEnabled,
                        ]);
                    } else {
                        // Create new record
                        WorkingHour::create([
                            'user_id' => $this->userId,
                            'day_of_week' => $day,
                            'start_time' => $this->startTime,
                            'end_time' => $this->endTime,
                            'is_enabled' => $this->isEnabled,
                        ]);
                    }
                }

                session()->flash('workingHourMessage', 'Working hours added for all days successfully.');
                $this->resetWorkingHourForm();
                $this->loadWorkingHours();
                return;
            }

            // Regular single day handling
            if ($this->editingWorkingHourId) {
                // Update existing record
                $workingHour = WorkingHour::findOrFail($this->editingWorkingHourId);
                $workingHour->update([
                    'day_of_week' => $this->dayOfWeek,
                    'start_time' => $this->startTime,
                    'end_time' => $this->endTime,
                    'is_enabled' => $this->isEnabled,
                ]);

                session()->flash('workingHourMessage', 'Working hours updated successfully.');
            } else {
                // Check for existing record for the same user and day
                $existing = WorkingHour::where('user_id', $this->userId)
                    ->where('day_of_week', $this->dayOfWeek)
                    ->first();

                if ($existing) {
                    // Update existing record instead of creating a new one
                    $existing->update([
                        'start_time' => $this->startTime,
                        'end_time' => $this->endTime,
                        'is_enabled' => $this->isEnabled,
                    ]);

                    session()->flash('workingHourMessage', 'Working hours updated successfully.');
                } else {
                    // Create new record
                    WorkingHour::create([
                        'user_id' => $this->userId,
                        'day_of_week' => $this->dayOfWeek,
                        'start_time' => $this->startTime,
                        'end_time' => $this->endTime,
                        'is_enabled' => $this->isEnabled,
                    ]);

                    session()->flash('workingHourMessage', 'Working hours added successfully.');
                }
            }
        } catch (\Exception $e) {
            session()->flash('workingHourError', 'Error: ' . $e->getMessage());
        }

        $this->resetWorkingHourForm();
        $this->loadWorkingHours();
    }

    public function deleteWorkingHour($id)
    {
        try {
            $workingHour = WorkingHour::findOrFail($id);
            $workingHour->delete();

            // Reload working hours without flashing a message
            $this->loadWorkingHours();

            // Close the confirmation dialog
            $this->deleteConfirmId = null;

            // No session flash message to avoid popup
        } catch (\Exception $e) {
            // Silent fail or log the error, but don't show a popup
            Log::error('Error deleting working hour: ' . $e->getMessage());
            // Close dialog even on error
            $this->deleteConfirmId = null;
        }
    }

    public function confirmDelete($id)
    {
        try {
            // Debug: Log the method call
            Log::info('confirmDelete called with ID: ' . $id);
            
            // Validate the working hour exists and belongs to this user
            $workingHour = WorkingHour::where('id', $id)
                ->where('user_id', $this->userId)
                ->first();
                
            if (!$workingHour) {
                Log::error('Working hour not found or does not belong to user. ID: ' . $id . ', User ID: ' . $this->userId);
                session()->flash('workingHourError', 'Working hour not found or access denied.');
                return;
            }
            
            // Set the confirmation ID
            $this->deleteConfirmId = $id;
            
            Log::info('Delete confirmation dialog should appear for ID: ' . $id);
            
            // Force a re-render to ensure the dialog appears
            $this->dispatch('$refresh');
            
        } catch (\Exception $e) {
            Log::error('Error in confirmDelete: ' . $e->getMessage());
            session()->flash('workingHourError', 'Error preparing delete confirmation: ' . $e->getMessage());
        }
    }

    public function cancelDelete()
    {
        Log::info('Delete confirmation cancelled');
        $this->deleteConfirmId = null;
    }

    public function confirmDeleteAction()
    {
        try {
            if ($this->deleteConfirmId) {
                Log::info('Executing delete for ID: ' . $this->deleteConfirmId);
                $this->deleteWorkingHour($this->deleteConfirmId);
                Log::info('Delete completed successfully');
            } else {
                Log::warning('confirmDeleteAction called but no deleteConfirmId set');
            }
        } catch (\Exception $e) {
            Log::error('Error in confirmDeleteAction: ' . $e->getMessage());
            $this->deleteConfirmId = null;
        }
    }

    public function directDelete($id)
    {
        try {
            Log::info('Direct delete called for ID: ' . $id);
            
            $workingHour = WorkingHour::findOrFail($id);
            $dayName = ucfirst($workingHour->day_of_week);
            
            $workingHour->delete();
            
            // Reload working hours
            $this->loadWorkingHours();
            
            session()->flash('success', "Working hours for {$dayName} deleted successfully.");
            Log::info('Direct delete completed successfully for ID: ' . $id);
            
        } catch (\Exception $e) {
            Log::error('Error in directDelete: ' . $e->getMessage());
            session()->flash('error', 'Error deleting working hours: ' . $e->getMessage());
        }
    }

    public function toggleWorkingHourStatus($id)
    {
        try {
            $workingHour = WorkingHour::findOrFail($id);
            $workingHour->update([
                'is_enabled' => !$workingHour->is_enabled,
            ]);

            // Update the workingHour in the local collection instead of reloading all
            foreach ($this->workingHours as $index => $wh) {
                if ($wh->id == $id) {
                    $this->workingHours[$index] = $workingHour->fresh();
                    break;
                }
            }

            // No session flash message to avoid popup
        } catch (\Exception $e) {
            // Silent fail or log the error, but don't show a popup
            Log::error('Error toggling working hour status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.users.edit', [
            'branches' => $this->branches,
            'roles' => $this->roles,
        ]);
    }
}
