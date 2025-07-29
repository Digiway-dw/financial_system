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

class AgentDashboard extends Component
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
    public $selectedBranches = [];
    public $agentLines = [];
    public $agentLinesTotalBalance = 0;
    public $branchSafes = [];
    public $totalSafesBalance = 0;
    public $searchedTransaction = null;
    public $selectedLineIds = [];
    public $isAdminOrSupervisor = false;

    // Sorting properties
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
        $this->isAdminOrSupervisor = $user->hasRole('admin') || $user->hasRole('general_supervisor');
        // Load all branches for selection
        $this->branches = collect($this->branchRepository->all());
        // Set default selected branches (all branches)
        if ($this->isAdminOrSupervisor) {
            $this->selectedBranches = $this->branches->pluck('id')->toArray();
        } else {
            $this->selectedBranches = $user->branch_id ? [$user->branch_id] : [];
        }
        $this->loadAgentData();

        // Global search by reference number (any transaction in the system)
        $ref = request()->query('reference_number');
        if ($ref) {
            $transaction = Transaction::where('reference_number', $ref)->first();
            if (!$transaction) {
                $transaction = CashTransaction::where('reference_number', $ref)->first();
            }
            $this->searchedTransaction = $transaction;
        }
    }

    public function updatedSelectedBranches()
    {
        // Check if "all" is selected
        if (in_array('all', $this->selectedBranches)) {
            $this->selectAllBranches();
            return;
        }

        $this->loadAgentData();
    }

    public function selectAllBranches()
    {
        $this->selectedBranches = $this->branches->pluck('id')->toArray();
        $this->loadAgentData();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }

        $this->loadAgentData();
    }

    private function loadAgentData()
    {
        $user = Auth::user();

        // Filter lines by selected branches (only active lines)
        $lines = collect($this->lineRepository->all())->filter(function ($line) {
            return in_array($line->branch_id, $this->selectedBranches) && $line->status === 'active';
        });

        // Apply sorting
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
        } elseif ($this->sortField === 'branch') {
            $lines = $lines->sortBy(function ($line) {
                return $line->branch ? $line->branch->name : '';
            });
        }

        if ($this->sortDirection === 'desc') {
            $lines = $lines->reverse();
        }

        // Enhance lines with usage data and color classes (standardized)
        $this->agentLines = $lines->map(function ($line) {
            // Keep the line as an object to preserve relationships
            $line->daily_remaining = $line->daily_remaining ?? 0;
            $line->daily_usage_class = '';
            if (
                isset($line->daily_limit, $line->daily_usage, $line->status) &&
                $line->daily_limit > 0 &&
                $line->daily_usage >= $line->daily_limit &&
                $line->status === 'frozen'
            ) {
                $line->daily_usage_class = 'bg-red-100 text-red-700 font-bold';
            }
            // monthly_usage is used directly for monthly receive
            return $line;
        });

        $this->agentLinesTotalBalance = $lines->sum('current_balance');

        // Load safes for selected branches
        $safes = collect($this->safeRepository->allWithBranch())->filter(function ($safe) {
            return in_array($safe['branch_id'] ?? $safe->branch_id, $this->selectedBranches);
        });

        // Calculate total transactions in selected branches (all users) for today only
        $today = Carbon::today();
        $this->totalTransactionsCount = Transaction::whereIn('branch_id', $this->selectedBranches)
            ->whereDate('created_at', $today)
            ->count()
            + CashTransaction::whereHas('safe', function ($q) {
                $q->whereIn('branch_id', $this->selectedBranches);
            })->whereDate('created_at', $today)->count();

        $this->branchSafes = $safes->map(function ($safe) use ($today) {
            $safeId = $safe['id'] ?? $safe->id;
            $branchId = $safe['branch_id'] ?? $safe->branch_id;

            // Count all cash transactions for this safe today
            $cashTransactions = CashTransaction::where('safe_id', $safeId)
                ->whereDate('created_at', $today)
                ->count();

            // Count all regular transactions for this branch today
            $regularTransactions = Transaction::where('branch_id', $branchId)
                ->whereDate('created_at', $today)
                ->count();

            $todaysTransactions = $cashTransactions + $regularTransactions;

            return [
                'name' => $safe['name'] ?? $safe->name ?? '',
                'current_balance' => $safe['current_balance'] ?? $safe->current_balance ?? 0,
                'todays_transactions' => $todaysTransactions,
                'branch_id' => $branchId,
            ];
        });

        // Calculate total safes balance
        $this->totalSafesBalance = collect($this->branchSafes)->sum('current_balance');
    }

    public function toggleSelectAllLines()
    {
        if (count($this->selectedLineIds) === $this->agentLines->count()) {
            $this->selectedLineIds = [];
        } else {
            $this->selectedLineIds = $this->agentLines->pluck('id')->toArray();
        }
    }

    public function toggleSelectLine($lineId)
    {
        if (($key = array_search($lineId, $this->selectedLineIds)) !== false) {
            unset($this->selectedLineIds[$key]);
        } else {
            $this->selectedLineIds[] = $lineId;
        }
        $this->selectedLineIds = array_values($this->selectedLineIds);
    }

    public function getSelectedLinesProperty()
    {
        return $this->agentLines->filter(function ($line) {
            return in_array($line->id, $this->selectedLineIds);
        });
    }

    public function getSelectedTotalsProperty()
    {
        $selected = $this->selectedLines;
        return [
            'current_balance' => $selected->sum('current_balance'),
            'daily_limit' => $selected->sum('daily_limit'),
            'monthly_limit' => $selected->sum('monthly_limit'),
            'daily_remaining' => $selected->sum('daily_remaining'),
            'monthly_remaining' => $selected->sum('monthly_remaining'),
            'daily_usage' => $selected->sum(function ($line) {
                $dailyStarting = $line->daily_starting_balance ?? 0;
                $current = $line->current_balance ?? 0;
                return max(0, $current - $dailyStarting);
            }),
            'monthly_usage' => $selected->sum(function ($line) {
                $monthlyStarting = $line->starting_balance ?? 0;
                $current = $line->current_balance ?? 0;
                return max(0, $current - $monthlyStarting);
            }),
        ];
    }

    public function render()
    {
        return view('livewire.agent-dashboard', [
            'user' => Auth::user(),
            'branches' => $this->branches,
            'selectedBranches' => $this->selectedBranches,
            'agentLines' => $this->agentLines,
            'agentLinesTotalBalance' => $this->agentLinesTotalBalance,
            'branchSafes' => $this->branchSafes,
            'totalSafesBalance' => $this->totalSafesBalance,
            'searchedTransaction' => $this->searchedTransaction,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'isAdminOrSupervisor' => $this->isAdminOrSupervisor,
        ]);
    }
}
