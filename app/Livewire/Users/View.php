<?php

namespace App\Livewire\Users;

use Livewire\Component;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use App\Models\WorkingHour;
use Spatie\Permission\Models\Role;

class View extends Component
{
    public $user;
    public $branch;
    public $role;
    public $transactions;
    public $loginHistories;
    public $workingHours;

    public function mount($userId)
    {
        $user = User::findOrFail($userId);
        
        // Check if current user can view this user
        if (!$this->canViewUser($user)) {
            abort(403, 'You are not authorized to view this user profile.');
        }
        
        $this->user = $user;
        $this->branch = $user->branch;
        $this->role = $user->getRoleNames()->first();
        $this->transactions = $user->transactions()->latest()->get();
        $this->loginHistories = $user->loginHistories()->orderByDesc('login_at')->get();

        // Load working hours
        $this->workingHours = WorkingHour::where('user_id', $userId)
            ->orderBy('day_of_week')
            ->get();
    }

    public function canViewUser($targetUser): bool
    {
        $currentUser = auth()->user();
        
        // Admin can view anyone except other admins, unless they are admin@financial.system
        if ($currentUser->hasRole('admin')) {
            if ($currentUser->email === 'admin@financial.system') {
                return true;
            }
            return !$targetUser->hasRole('admin') || $currentUser->id === $targetUser->id;
        }
        
        // Supervisor can view non-admin, non-supervisor users
        if ($currentUser->hasRole('general_supervisor')) {
            return !$targetUser->hasRole(['admin', 'general_supervisor']);
        }
        
        // Other users cannot view anyone
        return false;
    }

    public function render()
    {
        // Create a days array for displaying day names
        $days = [
            'monday' => 'Monday',
            'tuesday' => 'Tuesday',
            'wednesday' => 'Wednesday',
            'thursday' => 'Thursday',
            'friday' => 'Friday',
            'saturday' => 'Saturday',
            'sunday' => 'Sunday',
        ];

        return view('livewire.users.view', [
            'user' => $this->user,
            'branch' => $this->branch,
            'role' => $this->role,
            'transactions' => $this->transactions,
            'loginHistories' => $this->loginHistories,
            'workingHours' => $this->workingHours,
            'days' => $days,
        ]);
    }
}
