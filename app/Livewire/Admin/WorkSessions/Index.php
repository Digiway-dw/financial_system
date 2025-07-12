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
        }, 'user.branch'])
            ->orderBy('login_at', 'desc');

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
        }, 'user.branch'])
            ->orderBy('login_at', 'desc');

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

        $sessions = $query->paginate(15);

        // Calculate statistics for filtered sessions (using the full set, not just the current page)
        $this->calculateStatistics($this->getFilteredSessions());

        return view('livewire.admin.work-sessions.index', [
            'sessions' => $sessions,
        ]);
    }
}
