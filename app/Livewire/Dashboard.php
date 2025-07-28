<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\UserRepository;
use App\Domain\Interfaces\BranchRepository;
use App\Domain\Interfaces\CustomerRepository;
use App\Application\UseCases\ListFilteredTransactions;
use App\Application\UseCases\ListPendingTransactions;
use App\Application\UseCases\ViewLineBalanceAndUsage;
use Carbon\Carbon;
use App\Models\StartupSafeBalance;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;
use App\Models\Domain\Entities\Customer;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Safe;

class Dashboard extends Component
{
    private SafeRepository $safeRepository;
    private LineRepository $lineRepository;
    private TransactionRepository $transactionRepository;
    private UserRepository $userRepository;
    private BranchRepository $branchRepository;
    private CustomerRepository $customerRepository;
    private ListFilteredTransactions $listFilteredTransactionsUseCase;
    private ListPendingTransactions $listPendingTransactionsUseCase;
    private ViewLineBalanceAndUsage $viewLineBalanceAndUsageUseCase;

    public $branches = [];
    public $selectedBranchId = 'all';
    public $startupSafeBalance = 0;
    public $safesBalance = 0;
    public $sendTransactionsCount = 0;
    public $receiveTransactionsCount = 0;
    public $totalTransactionsCount = 0;
    public $selectedTraineeLineIds = [];
    public $traineeLines = [];

    // Sorting properties for line tables
    public $sortField = 'mobile_number';
    public $sortDirection = 'asc';

    public function boot(
        SafeRepository $safeRepository,
        LineRepository $lineRepository,
        TransactionRepository $transactionRepository,
        UserRepository $userRepository,
        BranchRepository $branchRepository,
        CustomerRepository $customerRepository,
        ListFilteredTransactions $listFilteredTransactionsUseCase,
        ListPendingTransactions $listPendingTransactionsUseCase,
        ViewLineBalanceAndUsage $viewLineBalanceAndUsageUseCase
    ) {
        $this->safeRepository = $safeRepository;
        $this->lineRepository = $lineRepository;
        $this->transactionRepository = $transactionRepository;
        $this->userRepository = $userRepository;
        $this->branchRepository = $branchRepository;
        $this->customerRepository = $customerRepository;
        $this->listFilteredTransactionsUseCase = $listFilteredTransactionsUseCase;
        $this->listPendingTransactionsUseCase = $listPendingTransactionsUseCase;
        $this->viewLineBalanceAndUsageUseCase = $viewLineBalanceAndUsageUseCase;
    }

    public function mount()
    {
        $user = Auth::user();
        if ($user->hasRole('branch_manager')) {
            // Always use assigned branch for branch manager
            $this->selectedBranchId = $user->branch_id;
            $this->updateBranchManagerMetrics();
        } elseif ($user->hasRole('auditor')) {
            // For auditors, use updateAuditorMetrics()
            $this->branches = collect($this->branchRepository->all());
            $this->selectedBranchId = request('branch', 'all');
            $this->updateAuditorMetrics();
        } elseif ($user->hasRole('trainee')) {
            // For trainees, load traineeLines for their branch (only active lines)
            $this->branches = collect($this->branchRepository->all());
            $this->selectedBranchId = $user->branch_id;
            $lines = collect($this->lineRepository->all())->where('branch_id', $user->branch_id ?? null)->where('status', 'active');
            $this->traineeLines = $lines->map(function ($line) {
                return is_object($line) ? $line : (object)$line;
            })->values()->all();
        } else {
            $this->branches = collect($this->branchRepository->all());
            $this->selectedBranchId = request('branch', 'all');
            $this->updateSupervisorMetrics();
        }
    }

    protected function updateBranchManagerMetrics()
    {
        $user = Auth::user();
        $branch = $user->branch;
        if ($branch) {
            // Startup safe balance for this branch
            $this->startupSafeBalance = optional($branch->startupSafeBalance)->balance ?? 0;
            // Safes balance for this branch
            $this->safesBalance = $branch->safes->sum('current_balance');
            // Total transactions count for this branch (today only)
            $today = Carbon::today();
            $ordinaryQuery = $branch->transactions()->whereDate('created_at', $today);
            $cashQuery = CashTransaction::whereHas('safe', function ($q) use ($branch) {
                $q->where('branch_id', $branch->id);
            })->whereDate('created_at', $today);
            $this->totalTransactionsCount = $ordinaryQuery->count() + $cashQuery->count();
        } else {
            $this->startupSafeBalance = 0;
            $this->safesBalance = 0;
            $this->totalTransactionsCount = 0;
        }
    }

    private function updateAuditorMetrics()
    {
        $branchId = $this->selectedBranchId;
        $today = Carbon::today();
        // Safes
        $safes = collect($this->safeRepository->allWithBranch());
        if ($branchId !== 'all') {
            $safes = $safes->where('branch_id', $branchId);
        }
        $this->safesBalance = $safes->sum('current_balance');

        // Startup Safe Balance (sum only for branches in $this->branches)
        $branchIds = $branchId === 'all'
            ? collect($this->branches)->pluck('id')->all()
            : [$branchId];
        $this->startupSafeBalance = \App\Models\StartupSafeBalance::whereIn('branch_id', $branchIds)
            ->where('date', $today->toDateString())
            ->sum('balance');

        // Total Transactions (ordinary + cash) for today only
        $ordinaryQuery = Transaction::query();
        $cashQuery = CashTransaction::query();
        if ($branchId !== 'all') {
            $ordinaryQuery->where('branch_id', $branchId);
            $cashQuery->whereHas('safe', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        $ordinaryQuery->whereDate('created_at', $today);
        $cashQuery->whereDate('created_at', $today);
        $this->totalTransactionsCount = $ordinaryQuery->count() + $cashQuery->count();
        logger(['auditorTotalTransactionsCount' => $this->totalTransactionsCount]);
    }

    private function updateSupervisorMetrics()
    {
        $today = Carbon::today();
        $branchId = $this->selectedBranchId;

        // Safes
        $safes = collect($this->safeRepository->allWithBranch());
        if ($branchId !== 'all') {
            $safes = $safes->where('branch_id', $branchId);
        }
        $this->safesBalance = $safes->sum('current_balance');

        // Startup Safe Balance (sum only for branches in $this->branches)
        $branchIds = $branchId === 'all'
            ? collect($this->branches)->pluck('id')->all()
            : [$branchId];
        $this->startupSafeBalance = \App\Models\StartupSafeBalance::whereIn('branch_id', $branchIds)
            ->where('date', $today->toDateString())
            ->sum('balance');

        // Total Transactions (ordinary + cash) for all dates (debugging)
        $ordinaryQuery = Transaction::query();
        $cashQuery = CashTransaction::query();
        if ($branchId !== 'all') {
            $ordinaryQuery->where('branch_id', $branchId);
            $cashQuery->whereHas('safe', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        // Only count today's transactions
        $ordinaryQuery->whereDate('created_at', $today);
        $cashQuery->whereDate('created_at', $today);
        $this->totalTransactionsCount = $ordinaryQuery->count() + $cashQuery->count();
        logger(['totalTransactionsCount' => $this->totalTransactionsCount]);
    }

    public function updatedSelectedBranchId($value)
    {
        $user = Auth::user();
        if ($user->hasRole('branch_manager')) {
            // Ignore updates for branch manager
            $this->selectedBranchId = $user->branch_id;
            $this->updateBranchManagerMetrics();
        } elseif ($user->hasRole('auditor')) {
            // For auditors, update metrics without date filter
            $this->updateAuditorMetrics();
        } else {
            $this->updateSupervisorMetrics();
        }
    }

    public function sortBy($field)
    {
        // Log the current URL parameters for debugging
        logger()->info('Sorting triggered', [
            'field' => $field,
            'current_sort_field' => $this->sortField,
            'current_sort_direction' => $this->sortDirection,
            'url_params' => request()->all(),
            'current_url' => request()->url(),
            'as_agent' => request()->query('as_agent'),
            'branches' => request()->query('branches'),
            'user_role' => Auth::user()->roles->pluck('name')->toArray()
        ]);
        
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleSelectAllTraineeLines()
    {
        if (isset($this->traineeLines) && count($this->selectedTraineeLineIds) === count($this->traineeLines)) {
            $this->selectedTraineeLineIds = [];
        } else {
            $this->selectedTraineeLineIds = collect($this->traineeLines)->pluck('id')->toArray();
        }
    }

    public function render()
    {
        $user = Auth::user();
        $data = [];
        $dashboardView = 'livewire.dashboard.trainee'; // Default to trainee dashboard

        // Debug logging for dashboard view selection
        logger()->info('Dashboard render', [
            'user_role' => $user->roles->pluck('name')->toArray(),
            'is_admin' => $user->hasRole('admin'),
        ]);

        // Always set branchSafeBalance for the user's branch
        $userBranch = $user->branch;
        $branchSafes = collect($this->safeRepository->all())->where('branch_id', $userBranch->id ?? null);
        $data['branchSafeBalance'] = $branchSafes->sum('current_balance');

        if ($user->hasRole('admin')) {
            $data['showAdminAgentToggle'] = true;
            $data['totalUsers'] = count($this->userRepository->all());
            $data['totalBranches'] = count($this->branchRepository->all());
            $data['totalLines'] = collect($this->lineRepository->all())->where('status', 'active')->count();
            $data['totalSafes'] = count($this->safeRepository->all());
            $data['totalCustomers'] = Customer::count();
            // Count both Transaction and CashTransaction for totalTransactions
            $data['totalTransactions'] = Transaction::count() + CashTransaction::count();
            $data['totalSafeBalance'] = collect($this->safeRepository->all())->sum('current_balance');

            $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
            $data['totalTransferred'] = $allTransactions['totals']['total_transferred'];
            $data['netProfits'] = $allTransactions['totals']['net_profit'];
            // Count all transactions and cash transactions that require approval
            $pendingTransactionsCount = Transaction::where('status', 'Pending')->where('deduction', '>', 0)->count();
            $pendingCashCount = CashTransaction::where('status', 'pending')->where('transaction_type', 'Withdrawal')->count();
            $data['pendingTransactionsCount'] = $pendingTransactionsCount + $pendingCashCount;
            // Add adminLines and adminLinesTotalBalance for the lines table (only active lines)
            $allLines = collect($this->lineRepository->all())->where('status', 'active');
            $adminLines = $allLines->map(function ($line) {
                $lineArray = is_object($line) ? $line->toArray() : $line;
                $lineArray['daily_remaining'] = isset($lineArray['daily_limit'], $lineArray['current_balance'])
                    ? max(0, $lineArray['daily_limit'] - $lineArray['current_balance'])
                    : 0;
                $lineArray['daily_usage_class'] = '';
                if (
                    isset($lineArray['daily_limit'], $lineArray['daily_usage'], $lineArray['status']) &&
                    $lineArray['daily_limit'] > 0 &&
                    $lineArray['daily_usage'] >= $lineArray['daily_limit'] &&
                    $lineArray['status'] === 'frozen'
                ) {
                    $lineArray['daily_usage_class'] = 'bg-red-100 text-red-700 font-bold';
                }
                return (object) $lineArray;
            });
            $data['adminLines'] = $adminLines;
            $data['adminLinesTotalBalance'] = $allLines->sum('current_balance');
            $dashboardView = 'livewire.dashboard.admin';
        } elseif ($user->hasRole('general_supervisor')) {
            // Supervisor dashboard metrics
            $data['branches'] = $this->branches;
            $data['selectedBranchId'] = $this->selectedBranchId;
            $data['startupSafeBalance'] = $this->startupSafeBalance;
            $data['safesBalance'] = $this->safesBalance;
            $data['totalTransactionsCount'] = $this->totalTransactionsCount;
            $data['supervisorName'] = $user->name;
            // Add admin name (first user with 'admin' role) using whereHas
            $adminUser = \App\Domain\Entities\User::whereHas('roles', function ($q) {
                $q->where('name', 'admin');
            })->first();
            $data['adminName'] = $adminUser ? $adminUser->name : null;
            if ($this->selectedBranchId !== 'all') {
                $branch = $this->branches->first(function ($b) {
                    return (string)$b->id === (string)$this->selectedBranchId;
                });
                $data['selectedBranchDetails'] = $branch ? [
                    'name' => $branch->name,
                ] : null;
            } else {
                $data['selectedBranchDetails'] = null;
            }
            // Add all required metrics for dashboard cards (no branch filtering for main supervisor dashboard)
            $data['totalUsers'] = count($this->userRepository->all());
            $data['totalBranches'] = count($this->branchRepository->all());
            $data['totalSafes'] = count($this->safeRepository->all());
            $data['totalLines'] = collect($this->lineRepository->all())->where('status', 'active')->count();
            $data['totalCustomers'] = Customer::count();
            // Count both Transaction and CashTransaction for totalTransactions
            $data['totalTransactions'] = Transaction::count() + CashTransaction::count();
            $data['totalSafeBalance'] = collect($this->safeRepository->all())->sum('current_balance');
            $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
            $data['totalTransferred'] = $allTransactions['totals']['total_transferred'] ?? 0;
            $data['netProfits'] = $allTransactions['totals']['net_profit'] ?? 0;
            $pendingTransactionsCount = Transaction::where('status', 'Pending')->where('deduction', '>', 0)->count();
            $pendingCashCount = CashTransaction::where('status', 'pending')->where('transaction_type', 'Withdrawal')->count();
            $data['pendingTransactionsCount'] = $pendingTransactionsCount + $pendingCashCount;
            // Add supervisorLines and supervisorLinesTotalBalance for the lines table (only active lines)
            $allLines = collect($this->lineRepository->all())->where('status', 'active');
            $supervisorLines = $allLines->map(function ($line) {
                $lineArray = is_object($line) ? $line->toArray() : $line;
                $lineArray['daily_remaining'] = isset($lineArray['daily_limit'], $lineArray['current_balance'])
                    ? max(0, $lineArray['daily_limit'] - $lineArray['current_balance'])
                    : 0;
                $lineArray['daily_usage_class'] = '';
                if (
                    isset($lineArray['daily_limit'], $lineArray['daily_usage'], $lineArray['status']) &&
                    $lineArray['daily_limit'] > 0 &&
                    $lineArray['daily_usage'] >= $lineArray['daily_limit'] &&
                    $lineArray['status'] === 'frozen'
                ) {
                    $lineArray['daily_usage_class'] = 'bg-red-100 text-red-700 font-bold';
                }
                return (object) $lineArray;
            });
            $data['supervisorLines'] = $supervisorLines;
            $data['supervisorLinesTotalBalance'] = $allLines->sum('current_balance');
            $dashboardView = 'livewire.dashboard.general_supervisor';
        } elseif ($user->hasRole('branch_manager')) {
            $userBranch = $user->branch;
            // Force branch manager to only see their assigned branch, ignore any selector
            $branchId = $userBranch->id ?? null;
            $data['branchName'] = $userBranch->name ?? 'N/A';

            $branchSafes = collect($this->safeRepository->all())->where('branch_id', $branchId);
            $data['branchSafeBalance'] = $branchSafes->sum('current_balance');

            $pendingTransactions = $this->listPendingTransactionsUseCase->execute();
            $data['branchPendingTransactionsCount'] = collect($pendingTransactions)->where('branch_id', $branchId)->count();

            $branchUsers = collect($this->userRepository->getUsersByBranch($branchId));
            $branchUsers = $branchUsers->filter(function ($u) {
                return !$u->hasRole('admin') && !$u->hasRole('general_supervisor');
            });
            $data['branchUsersCount'] = $branchUsers->count();
            // Fetch all active lines for the branch
            $branchLines = Line::where('branch_id', $branchId)->where('status', 'active')->get();
            
            // Apply sorting to branch manager lines
            if ($this->sortField === 'mobile_number') {
                $branchLines = $branchLines->sortBy('mobile_number');
            } elseif ($this->sortField === 'current_balance') {
                $branchLines = $branchLines->sortBy('current_balance');
            } elseif ($this->sortField === 'daily_limit') {
                $branchLines = $branchLines->sortBy('daily_limit');
            } elseif ($this->sortField === 'monthly_limit') {
                $branchLines = $branchLines->sortBy('monthly_limit');
            } elseif ($this->sortField === 'network') {
                $branchLines = $branchLines->sortBy('network');
            } elseif ($this->sortField === 'status') {
                $branchLines = $branchLines->sortBy('status');
            }

            if ($this->sortDirection === 'desc') {
                $branchLines = $branchLines->reverse();
            }
            
            $data['branchLines'] = $branchLines;
            $data['branchLinesTotalBalance'] = $branchLines->sum('current_balance');
            // Metrics table values
            $today = \Carbon\Carbon::today();
            $startup = \App\Models\StartupSafeBalance::where('branch_id', $branchId)
                ->where('date', $today->toDateString())
                ->first();
            $data['startupSafeBalance'] = $startup ? $startup->balance : 0;
            $safes = collect($this->safeRepository->allWithBranch())->where('branch_id', $branchId);
            $data['safesBalance'] = $safes->sum('current_balance');
            $ordinaryQuery = Transaction::query()->where('branch_id', $branchId)->whereDate('created_at', $today);
            $cashQuery = CashTransaction::query()->whereHas('safe', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->whereDate('created_at', $today);
            $data['totalTransactionsCount'] = $ordinaryQuery->count() + $cashQuery->count();
            $dashboardView = 'livewire.dashboard.branch_manager';
        } elseif ($user->hasRole('agent') || $user->hasRole('trainee')) {
            $lines = collect($this->lineRepository->all())->where('branch_id', $user->branch_id ?? null)->where('status', 'active');

            // Apply sorting to lines
            if ($this->sortField === 'mobile_number') {
                $lines = $lines->sortBy('mobile_number');
            } elseif ($this->sortField === 'current_balance') {
                $lines = $lines->sortBy('current_balance');
            } elseif ($this->sortField === 'daily_limit') {
                $lines = $lines->sortBy('daily_limit');
            } elseif ($this->sortField === 'daily_usage') {
                $lines = $lines->sortBy('daily_usage');
            } elseif ($this->sortField === 'monthly_limit') {
                $lines = $lines->sortBy('monthly_limit');
            } elseif ($this->sortField === 'monthly_usage') {
                $lines = $lines->sortBy('monthly_usage');
            } elseif ($this->sortField === 'network') {
                $lines = $lines->sortBy('network');
            } elseif ($this->sortField === 'status') {
                $lines = $lines->sortBy('status');
            }

            if ($this->sortDirection === 'desc') {
                $lines = $lines->reverse();
            }

            // Enhance lines with usage data and color classes
            $linesWithUsage = $lines->map(function ($line) {
                $lineArray = is_object($line) ? $line->toArray() : $line;
                $lineArray['daily_usage_class'] = '';
                if (
                    isset($lineArray['daily_limit'], $lineArray['daily_usage'], $lineArray['status']) &&
                    $lineArray['daily_limit'] > 0 &&
                    $lineArray['daily_usage'] >= $lineArray['daily_limit'] &&
                    $lineArray['status'] === 'frozen'
                ) {
                    $lineArray['daily_usage_class'] = 'bg-red-100 text-red-700 font-bold';
                }
                return (object) $lineArray;
            });

            if ($user->hasRole('agent')) {
                $data['agentLines'] = $linesWithUsage;
                $data['agentLinesTotalBalance'] = $lines->sum('current_balance');
            } elseif ($user->hasRole('trainee')) {
                $data['traineeLines'] = $linesWithUsage;
                $data['traineeLinesTotalBalance'] = $lines->sum('current_balance');
            }

            $data['agentTotalBalance'] = $lines->sum('current_balance');

            $agentTransactions = collect($this->transactionRepository->all())->where('agent_id', $user->id);
            $data['agentTotalTransferred'] = $agentTransactions->sum('amount');
            $data['agentPendingTransactionsCount'] = $agentTransactions->where('status', 'Pending')->count();

            // Add metrics table data for agent/trainee dashboard
            $userBranch = $user->branch;
            $branchId = $userBranch->id ?? null;

            // Startup Safe Balance for agent's branch (sum for the branch and date)
            $today = \Carbon\Carbon::today();
            $branchStartupBalance = \App\Models\StartupSafeBalance::where('branch_id', $branchId)
                ->where('date', $today->toDateString())
                ->sum('balance');

            // Safes for agent's branch (list)
            $branchSafes = collect($this->safeRepository->allWithBranch())->where('branch_id', $branchId)->values();
            $data['branchSafes'] = $branchSafes->map(function ($safe) use ($branchStartupBalance, $today, $branchId) {
                // Count BOTH CashTransaction and regular Transaction records for this branch (all users)
                $safeId = $safe['id'] ?? $safe->id;
                
                $cashTransactions = \App\Models\Domain\Entities\CashTransaction::where('safe_id', $safeId)
                    ->whereDate('created_at', $today)
                    ->count();
                $regularTransactions = \App\Models\Domain\Entities\Transaction::where('branch_id', $branchId)
                    ->whereDate('created_at', $today)
                    ->count();
                $todaysTransactions = $cashTransactions + $regularTransactions;
                return [
                    'name' => $safe['name'] ?? $safe->name ?? '',
                    'current_balance' => $safe['current_balance'] ?? $safe->current_balance ?? 0,
                    'startup_balance' => $branchStartupBalance ?? 0,
                    'todays_transactions' => $todaysTransactions,
                ];
            });
            $data['safesBalance'] = $branchSafes->sum('current_balance');

            // Total Transactions Count for agent's branch (not per user)
            $ordinaryQuery = \App\Models\Domain\Entities\Transaction::query()->where('branch_id', $branchId)->whereDate('created_at', $today);
            $cashQuery = \App\Models\Domain\Entities\CashTransaction::query()->whereHas('safe', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            })->whereDate('created_at', $today);
            $data['totalTransactionsCount'] = $ordinaryQuery->count() + $cashQuery->count();

            // Today's transactions for this agent (both regular and cash transactions)
            $regularTransactions = Transaction::where('agent_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();
            $cashTransactions = \App\Models\Domain\Entities\CashTransaction::where('agent_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();
            $data['agentTodayTransactionsCount'] = $regularTransactions + $cashTransactions;

            // Transaction search by reference number (agent/trainee)
            if (request()->query('search_transaction') && request()->query('reference_number')) {
                $searched = Transaction::where('agent_id', $user->id)
                    ->where('reference_number', request()->query('reference_number'))
                    ->first();
                $data['searchedTransaction'] = $searched;
            }

            $dashboardView = $user->hasRole('agent') ? 'livewire.dashboard.agent' : 'livewire.dashboard.trainee';
        } elseif ($user->hasRole('auditor')) {
            // Auditor dashboard metrics - use updateAuditorMetrics() for proper counting
            $data['branches'] = $this->branches;
            $data['selectedBranchId'] = $this->selectedBranchId;
            $data['startupSafeBalance'] = $this->startupSafeBalance;
            $data['safesBalance'] = $this->safesBalance;
            $data['totalTransactionsCount'] = $this->totalTransactionsCount; // Use the count from updateAuditorMetrics()
            
            $data['auditorName'] = $user->name;
            if ($this->selectedBranchId !== 'all') {
                $branch = $this->branches->first(function ($b) {
                    return (string)$b->id === (string)$this->selectedBranchId;
                });
                $data['selectedBranchDetails'] = $branch ? [
                    'name' => $branch->name,
                ] : null;
            } else {
                $data['selectedBranchDetails'] = null;
            }
            // Add branchSafes for the summary table
            $today = \Carbon\Carbon::today();
            if ($this->selectedBranchId !== 'all') {
                $branchSafes = collect($this->safeRepository->allWithBranch())->where('branch_id', $this->selectedBranchId)->values();
            } else {
                $branchSafes = collect($this->safeRepository->allWithBranch());
            }
            $data['branchSafes'] = $branchSafes->map(function ($safe) use ($today) {
                // Count BOTH CashTransaction and regular Transaction records for this safe's branch
                $safeId = $safe['id'] ?? $safe->id;
                $branchId = $safe['branch_id'] ?? $safe->branch_id;
                
                $cashTransactions = \App\Models\Domain\Entities\CashTransaction::where('safe_id', $safeId)
                    ->whereDate('created_at', $today)
                    ->count();
                $regularTransactions = \App\Models\Domain\Entities\Transaction::where('branch_id', $branchId)
                    ->whereDate('created_at', $today)
                    ->count();
                
                $todaysTransactions = $cashTransactions + $regularTransactions;
                
                return [
                    'name' => $safe['name'] ?? $safe->name ?? '',
                    'current_balance' => $safe['current_balance'] ?? $safe->current_balance ?? 0,
                    'todays_transactions' => $todaysTransactions,
                ];
            });
            $dashboardView = 'livewire.dashboard.auditor';
        }

        return view($dashboardView, array_merge(['user' => $user], $data, [
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'currentUrl' => request()->url(),
            'currentParams' => request()->all(),
        ]));
    }
}
