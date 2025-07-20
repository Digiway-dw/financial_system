<?php

namespace App\Livewire\Admin\WorkSessions;

use App\Application\Services\WorkSessionService;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\WorkSession;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $selectedUser = null;
    public $dateFrom = null;
    public $dateTo = null;
    public $selectedBranch = null;
    public $branches = [];
    public $users = [];
    public $totalHours = 0;
    public $totalSessions = 0;
    public $averageSessionLength = 0;

    public $sortField = 'login_at';
    public $sortDirection = 'desc';

    // Session lifetime settings
    public $sessionLifetime = 120; // Default 2 hours
    public $showSessionLifetimeModal = false;

    public function mount()
    {
        // Check permissions
        if (!Gate::allows('view-work-sessions')) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to view this page.');
        }

        // Get all branches
        $this->branches = \App\Models\Domain\Entities\Branch::orderBy('name')->get();

        // Initialize date filters to the current week
        $this->dateFrom = now()->startOfWeek()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');

        // Load users based on permissions
        $this->loadUsers();

        // Load current session lifetime
        $this->loadSessionLifetime();
    }

    /**
     * Load the current session lifetime setting
     */
    public function loadSessionLifetime()
    {
        $workSessionService = app(WorkSessionService::class);
        $this->sessionLifetime = $workSessionService->getSessionLifetime();
    }

    /**
     * Open the session lifetime modal
     */
    public function openSessionLifetimeModal()
    {
        $this->loadSessionLifetime();
        $this->showSessionLifetimeModal = true;
    }

    /**
     * Close the session lifetime modal
     */
    public function closeSessionLifetimeModal()
    {
        $this->showSessionLifetimeModal = false;
    }

    /**
     * Save the session lifetime setting
     */
    public function saveSessionLifetime()
    {
        // Validate the input
        $this->validate([
            'sessionLifetime' => 'required|integer|min:1|max:1440', // Max 24 hours
        ]);

        $workSessionService = app(WorkSessionService::class);
        $success = $workSessionService->updateSessionLifetime($this->sessionLifetime);

        if ($success) {
            session()->flash('message', 'Session lifetime updated successfully.');
        } else {
            session()->flash('error', 'Failed to update session lifetime.');
        }

        $this->closeSessionLifetimeModal();
    }

    public function loadUsers()
    {
        $query = User::query()->orderBy('name');

        // Filter by branch if selected
        if ($this->selectedBranch) {
            $query->where('branch_id', $this->selectedBranch);
        }

        $this->users = $query->get();
    }

    public function updatedSelectedBranch()
    {
        $this->loadUsers();
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function calculateStatistics($sessions)
    {
        $this->totalSessions = $sessions->count();
        $this->totalHours = $sessions->sum('duration_minutes') / 60;
        $this->averageSessionLength = $this->totalSessions > 0
            ? $sessions->sum('duration_minutes') / $this->totalSessions
            : 0;
    }

    public function exportCsv()
    {
        $sessions = $this->getFilteredSessions();
        $workSessionService = app(WorkSessionService::class);

        return $workSessionService->exportToCsv($sessions);
    }

    public function exportExcel()
    {
        $sessions = $this->getFilteredSessions();
        $workSessionService = app(WorkSessionService::class);

        return $workSessionService->exportToExcel($sessions);
    }

    private function getFilteredSessions()
    {
        $query = WorkSession::with(['user' => function ($q) {
            $q->withTrashed(); // Include deleted users
        }, 'user.branch']);

        // Handle different sorting scenarios
        if ($this->sortField === 'status') {
            // Status sorting: use logout_at (null = active, not null = closed)
            $query->orderBy('logout_at', $this->sortDirection === 'asc' ? 'asc' : 'desc')
                ->orderBy('login_at', 'desc'); // Secondary sort for consistent results
        } elseif ($this->sortField === 'branch_id') {
            // Branch sorting: use collection sorting after loading
            $sessions = $query->get();
            $sortedSessions = $sessions->sortBy(function ($session) {
                return $session->user->branch->name ?? '';
            });

            if ($this->sortDirection === 'desc') {
                $sortedSessions = $sortedSessions->reverse();
            }

            return $sortedSessions->values();
        } elseif ($this->sortField === 'user_id') {
            // User sorting: use collection sorting after loading
            $sessions = $query->get();
            $sortedSessions = $sessions->sortBy(function ($session) {
                return $session->user->name ?? '';
            });

            if ($this->sortDirection === 'desc') {
                $sortedSessions = $sortedSessions->reverse();
            }

            return $sortedSessions->values();
        } else {
            // Direct column sorting for existing columns
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        // Filter by user if selected
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        // Filter by branch if selected
        if ($this->selectedBranch) {
            $query->whereHas('user', function ($q) {
                $q->where('branch_id', $this->selectedBranch);
            });
        }

        // Filter by date range
        if ($this->dateFrom) {
            $query->whereDate('login_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('login_at', '<=', $this->dateTo);
        }

        return $query->get();
    }

    /**
     * Update the status of active sessions
     * Checks if any sessions that show as active have actually expired
     */
    public function updateSessionStatuses()
    {
        // Find active sessions
        $activeSessions = WorkSession::whereNull('logout_at')->get();

        foreach ($activeSessions as $session) {
            // If the session is more than 5 minutes old and still active, mark as logged out
            if ($session->login_at->diffInMinutes(now()) > 5) {
                $session->logout_at = now();
                $session->calculateDuration();
                $session->save();
            }
        }

        // Show a notification
        session()->flash('message', 'Session statuses updated successfully');
    }

    public function resetFilters()
    {
        $this->selectedUser = null;
        $this->selectedBranch = null;
        $this->dateFrom = now()->startOfWeek()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
        $this->resetPage();
    }

    public function render()
    {
        $query = WorkSession::with(['user' => function ($q) {
            $q->withTrashed(); // Include deleted users
        }, 'user.branch']);

        // Filter by user if selected
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        // Filter by branch if selected
        if ($this->selectedBranch) {
            $query->whereHas('user', function ($q) {
                $q->where('branch_id', $this->selectedBranch);
            });
        }

        // Filter by date range
        if ($this->dateFrom) {
            $query->whereDate('login_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('login_at', '<=', $this->dateTo);
        }

        // Handle different sorting scenarios
        if ($this->sortField === 'status') {
            // Status sorting: use logout_at (null = active, not null = closed)
            $query->orderBy('logout_at', $this->sortDirection === 'asc' ? 'asc' : 'desc')
                ->orderBy('login_at', 'desc'); // Secondary sort for consistent results
        } elseif ($this->sortField === 'branch_id') {
            // Branch sorting: join with users and branches tables
            $query->leftJoin('users', 'work_sessions.user_id', '=', 'users.id')
                ->leftJoin('branches', 'users.branch_id', '=', 'branches.id')
                ->orderBy('branches.name', $this->sortDirection)
                ->orderBy('work_sessions.login_at', 'desc') // Secondary sort
                ->select('work_sessions.*');
        } elseif ($this->sortField === 'user_id') {
            // User sorting: join with users table
            $query->leftJoin('users', 'work_sessions.user_id', '=', 'users.id')
                ->orderBy('users.name', $this->sortDirection)
                ->orderBy('work_sessions.login_at', 'desc') // Secondary sort
                ->select('work_sessions.*');
        } else {
            // Direct column sorting for existing columns
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        $sessions = $query->paginate(15);

        // Calculate statistics for filtered sessions (using the full set, not just the current page)
        $this->calculateStatistics($this->getFilteredSessions());

        return view('livewire.admin.work-sessions.index', [
            'sessions' => $sessions,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
