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
            // Total transactions count for this branch
            $this->totalTransactionsCount = $branch->transactions()->count();
        } else {
            $this->startupSafeBalance = 0;
            $this->safesBalance = 0;
            $this->totalTransactionsCount = 0;
        }
    }

    private function updateAuditorMetrics()
    {
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
            ->where('date', Carbon::today()->toDateString())
            ->sum('balance');

        // Total Transactions (ordinary + cash) - NO DATE FILTER for auditors
        $ordinaryQuery = Transaction::query();
        $cashQuery = CashTransaction::query();
        if ($branchId !== 'all') {
            $ordinaryQuery->where('branch_id', $branchId);
            $cashQuery->whereHas('safe', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }
        // NO date filter - count ALL transactions
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

    public function render()
    {
        $user = Auth::user();
        $data = [];
        $dashboardView = 'livewire.dashboard.trainee'; // Default to trainee dashboard

        // Always set branchSafeBalance for the user's branch
        $userBranch = $user->branch;
        $branchSafes = collect($this->safeRepository->all())->where('branch_id', $userBranch->id ?? null);
        $data['branchSafeBalance'] = $branchSafes->sum('current_balance');

        if ($user->hasRole('admin') && request()->query('as_agent')) {
            // Admin as agent dashboard: show lines filtered by selected branches
            $allBranches = collect($this->branchRepository->all());
            $selectedBranches = request()->query('branches');
            if ($selectedBranches) {
                $selectedBranches = is_array($selectedBranches) ? $selectedBranches : explode(',', $selectedBranches);
                $lines = collect($this->lineRepository->all())->whereIn('branch_id', $selectedBranches);
                $safes = collect($this->safeRepository->allWithBranch())->whereIn('branch_id', $selectedBranches);
            } else {
                $lines = collect($this->lineRepository->all());
                $safes = collect($this->safeRepository->allWithBranch());
            }
            $data['branches'] = $allBranches;
            $data['selectedBranches'] = $selectedBranches ?? [];
            $data['agentLines'] = $lines;
            $data['agentLinesTotalBalance'] = $lines->sum('current_balance');
            $data['agentTotalBalance'] = $lines->sum('current_balance');
            $data['showAdminAgentToggle'] = true;
            // Provide branchSafes for the agent dashboard summary table
            $data['branchSafes'] = $safes->map(function ($safe) {
                return [
                    'name' => $safe['name'] ?? '',
                    'current_balance' => $safe['current_balance'] ?? 0,
                    'startup_balance' => 0, // Optionally fetch startup balance if needed
                ];
            })->values();
            $data['agentTodayTransactionsCount'] = 0;
            $dashboardView = 'livewire.dashboard.agent';
        } else if ($user->hasRole('admin')) {
            $data['showAdminAgentToggle'] = true;
            $data['totalUsers'] = count($this->userRepository->all());
            $data['totalBranches'] = count($this->branchRepository->all());
            $data['totalLines'] = count($this->lineRepository->all());
            $data['totalSafes'] = count($this->safeRepository->all());
            $data['totalCustomers'] = Customer::count();
            $data['totalTransactions'] = count($this->transactionRepository->all());
            $data['totalSafeBalance'] = collect($this->safeRepository->all())->sum('current_balance');

            $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
            $data['totalTransferred'] = $allTransactions['totals']['total_transferred'];
            $data['netProfits'] = $allTransactions['totals']['net_profit'];
            // Count all transactions and cash transactions that require approval
            $pendingTransactionsCount = Transaction::where('status', 'Pending')->where('deduction', '>', 0)->count();
            $pendingCashCount = CashTransaction::where('status', 'pending')->where('transaction_type', 'Withdrawal')->count();
            $data['pendingTransactionsCount'] = $pendingTransactionsCount + $pendingCashCount;
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
            $data['totalLines'] = count($this->lineRepository->all());
            $data['totalCustomers'] = Customer::count();
            $data['totalTransactions'] = count($this->transactionRepository->all());
            $data['totalSafeBalance'] = collect($this->safeRepository->all())->sum('current_balance');
            $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
            $data['totalTransferred'] = $allTransactions['totals']['total_transferred'] ?? 0;
            $data['netProfits'] = $allTransactions['totals']['net_profit'] ?? 0;
            $pendingTransactionsCount = Transaction::where('status', 'Pending')->where('deduction', '>', 0)->count();
            $pendingCashCount = CashTransaction::where('status', 'pending')->where('transaction_type', 'Withdrawal')->count();
            $data['pendingTransactionsCount'] = $pendingTransactionsCount + $pendingCashCount;

            // Add agent-specific data only when in agent view
            if (request()->query('as_agent')) {
                // Set the toggle flag for supervisors to show branch selector
                $data['showAdminAgentToggle'] = true;
                
                // Branch selection data
                $data['selectedBranches'] = request()->query('branches', []);
                if (empty($data['selectedBranches']) || in_array('all', $data['selectedBranches'])) {
                    $data['selectedBranches'] = $this->branches->pluck('id')->toArray();
                }

                // Filter data based on selected branches
                $selectedBranchIds = $data['selectedBranches'];
                if (!empty($selectedBranchIds) && !in_array('all', $selectedBranchIds)) {
                    // Filter safes by selected branches
                    $branchSafes = collect($this->safeRepository->allWithBranch())->filter(function ($safe) use ($selectedBranchIds) {
                        return in_array($safe['branch_id'] ?? $safe->branch_id, $selectedBranchIds);
                    });
                    
                    // Filter lines by selected branches
                    $allLines = collect($this->lineRepository->all())->filter(function ($line) use ($selectedBranchIds) {
                        return in_array($line->branch_id, $selectedBranchIds);
                    });
                } else {
                    // Show all safes and lines
                    $branchSafes = collect($this->safeRepository->allWithBranch());
                    $allLines = collect($this->lineRepository->all());
                }

                // Transaction search functionality
                if (request()->query('search_transaction') && request()->query('reference_number')) {
                    $searched = Transaction::where('reference_number', request()->query('reference_number'))->first();
                    $data['searchedTransaction'] = $searched;
                }

                // Safe summary data (filtered by selected branches)
                $today = \Carbon\Carbon::today();
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

                // Lines overview data (filtered by selected branches)
                $data['agentLines'] = $allLines->map(function ($line) {
                    $lineArray = is_object($line) ? $line->toArray() : $line;
                    
                    // Add color classes for daily usage
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
                $data['agentLinesTotalBalance'] = $allLines->sum('current_balance');
            }
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
            // Fetch all lines for the branch
            $branchLines = Line::where('branch_id', $branchId)->get();
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
            $ordinaryQuery = Transaction::query()->where('branch_id', $branchId);
            $cashQuery = CashTransaction::query()->whereHas('safe', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
            $data['totalTransactionsCount'] = $ordinaryQuery->count() + $cashQuery->count();
            $dashboardView = 'livewire.dashboard.branch_manager';
        } elseif ($user->hasRole('agent') || $user->hasRole('trainee')) {
            $agentLines = collect($this->lineRepository->all())->where('branch_id', $user->branch_id ?? null);

            // Enhance agent lines with usage data and color classes
            $agentLinesWithUsage = $agentLines->map(function ($line) {
                $lineArray = is_object($line) ? $line->toArray() : $line;

                // Add color classes for daily usage
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

            $data['agentLines'] = $agentLinesWithUsage;
            $data['agentLinesTotalBalance'] = $agentLines->sum('current_balance');
            $data['agentTotalBalance'] = $agentLines->sum('current_balance');

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
                // Count BOTH CashTransaction and regular Transaction records for this branch
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
            $ordinaryQuery = \App\Models\Domain\Entities\Transaction::query()->where('branch_id', $branchId);
            $cashQuery = \App\Models\Domain\Entities\CashTransaction::query()->whereHas('safe', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
            $data['totalTransactionsCount'] = $ordinaryQuery->count() + $cashQuery->count();

            // Today's transactions for this agent
            $data['agentTodayTransactionsCount'] = Transaction::where('agent_id', $user->id)
                ->whereDate('created_at', $today)
                ->count();

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

        return view($dashboardView, array_merge(['user' => $user], $data));
    }
}
