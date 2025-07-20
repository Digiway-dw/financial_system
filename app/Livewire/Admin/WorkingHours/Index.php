<?php

namespace App\Livewire\Admin\WorkingHours;

use App\Domain\Entities\User;
use App\Models\WorkingHour;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selectedUser = null;
    public $users = [];
    public $workingHours = [];
    public $days = [
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
    ];

    // Form fields
    public $userId;
    public $dayOfWeek;
    public $startTime;
    public $endTime;
    public $isEnabled = true;
    public $editingId = null;

    // Validation rules
    protected $rules = [
        'userId' => 'required|exists:users,id',
        'dayOfWeek' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        'startTime' => 'required|date_format:H:i',
        'endTime' => 'required|date_format:H:i|after:startTime',
        'isEnabled' => 'boolean',
    ];

    public function mount()
    {
        // Check permissions
        if (!Gate::allows('manage-working-hours')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view this page.');
        }

        // Load users
        $this->loadUsers();
    }

    public function loadUsers()
    {
        // Get all users except admin
        $this->users = User::whereDoesntHave('roles', function ($query) {
            $query->where('name', 'admin');
        })->orderBy('name')->get();

        if ($this->selectedUser) {
            $this->loadWorkingHours();
        }
    }

    public function updatedSelectedUser()
    {
        $this->loadWorkingHours();
        $this->resetForm();
    }

    public function loadWorkingHours()
    {
        if ($this->selectedUser) {
            $this->workingHours = WorkingHour::where('user_id', $this->selectedUser)
                ->orderBy('day_of_week')
                ->get();
        } else {
            $this->workingHours = [];
        }
    }

    public function resetForm()
    {
        $this->userId = $this->selectedUser;
        $this->dayOfWeek = null;
        $this->startTime = '09:00';
        $this->endTime = '17:00';
        $this->isEnabled = true;
        $this->editingId = null;
        $this->resetValidation();
    }

    public function edit($id)
    {
        $workingHour = WorkingHour::findOrFail($id);
        $this->userId = $workingHour->user_id;
        $this->dayOfWeek = $workingHour->day_of_week;
        $this->startTime = Carbon::parse($workingHour->start_time)->format('H:i');
        $this->endTime = Carbon::parse($workingHour->end_time)->format('H:i');
        $this->isEnabled = $workingHour->is_enabled;
        $this->editingId = $id;
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->editingId) {
                // Update existing record
                $workingHour = WorkingHour::findOrFail($this->editingId);
                $workingHour->update([
                    'user_id' => $this->userId,
                    'day_of_week' => $this->dayOfWeek,
                    'start_time' => $this->startTime,
                    'end_time' => $this->endTime,
                    'is_enabled' => $this->isEnabled,
                ]);

                session()->flash('message', 'Working hours updated successfully.');
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

                    session()->flash('message', 'Working hours updated successfully.');
                } else {
                    // Create new record
                    WorkingHour::create([
                        'user_id' => $this->userId,
                        'day_of_week' => $this->dayOfWeek,
                        'start_time' => $this->startTime,
                        'end_time' => $this->endTime,
                        'is_enabled' => $this->isEnabled,
                    ]);

                    session()->flash('message', 'Working hours created successfully.');
                }
            }

            $this->loadWorkingHours();
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Error saving working hours: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $workingHour = WorkingHour::findOrFail($id);
            $workingHour->delete();

            session()->flash('message', 'Working hours deleted successfully.');
            $this->loadWorkingHours();
        } catch (\Exception $e) {
            session()->flash('error', 'Error deleting working hours: ' . $e->getMessage());
        }
    }

    public function toggleStatus($id)
    {
        try {
            $workingHour = WorkingHour::findOrFail($id);
            $workingHour->update([
                'is_enabled' => !$workingHour->is_enabled,
            ]);

            $status = $workingHour->is_enabled ? 'enabled' : 'disabled';
            session()->flash('message', "Working hours {$status} successfully.");
            $this->loadWorkingHours();
        } catch (\Exception $e) {
            session()->flash('error', 'Error toggling status: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.working-hours.index');
    }
}
