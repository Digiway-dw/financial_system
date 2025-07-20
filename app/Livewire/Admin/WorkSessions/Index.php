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
        return $this->buildBaseQuery()->get();
    }

    private function buildBaseQuery()
    {
        $query = WorkSession::with(['user' => function ($q) {
            $q->withTrashed(); // Include deleted users
        }, 'user.branch']);

        // Apply filters first
        if ($this->selectedUser) {
            $query->where('user_id', $this->selectedUser);
        }

        if ($this->selectedBranch) {
            $query->whereHas('user', function ($q) {
                $q->where('branch_id', $this->selectedBranch);
            });
        }

        if ($this->dateFrom) {
            $query->whereDate('login_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('login_at', '<=', $this->dateTo);
        }

        // Apply sorting
        if ($this->sortField === 'status') {
            $query->orderBy('logout_at', $this->sortDirection === 'asc' ? 'asc' : 'desc')
                ->orderBy('login_at', 'desc');
        } elseif (in_array($this->sortField, ['login_at', 'logout_at', 'duration_minutes', 'ip_address', 'user_agent'])) {
            $query->orderBy($this->sortField, $this->sortDirection);
        } else {
            // Default sorting
            $query->orderBy('login_at', 'desc');
        }

        return $query;
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
        // Handle special sorting cases that require collection sorting
        if (in_array($this->sortField, ['branch_id', 'user_id'])) {
            $allSessions = $this->buildBaseQuery()->get();
            
            if ($this->sortField === 'branch_id') {
                $sortedSessions = $allSessions->sortBy(function ($session) {
                    return $session->user->branch->name ?? '';
                });
            } else { // user_id
                $sortedSessions = $allSessions->sortBy(function ($session) {
                    return $session->user->name ?? '';
                });
            }
            
            if ($this->sortDirection === 'desc') {
                $sortedSessions = $sortedSessions->reverse();
            }
            
            // Manual pagination for sorted collection
            $currentPage = request()->get('page', 1);
            $perPage = 15;
            $sessions = new \Illuminate\Pagination\LengthAwarePaginator(
                $sortedSessions->forPage($currentPage, $perPage)->values(),
                $sortedSessions->count(),
                $perPage,
                $currentPage,
                ['path' => request()->url(), 'pageName' => 'page']
            );
        } else {
            // Use database-level sorting and pagination for direct columns
            $sessions = $this->buildBaseQuery()->paginate(15);
        }

        // Calculate statistics for filtered sessions (using the full set, not just the current page)
        $this->calculateStatistics($this->getFilteredSessions());

        return view('livewire.admin.work-sessions.index', [
            'sessions' => $sessions,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
}
