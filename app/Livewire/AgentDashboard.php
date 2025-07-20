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
    public $searchedTransaction = null;

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
        // Load all branches for selection
        $this->branches = collect($this->branchRepository->all());
        // Set default selected branches (all branches)
        $this->selectedBranches = $this->branches->pluck('id')->toArray();
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
        
        // Filter lines by selected branches
        $lines = collect($this->lineRepository->all())->filter(function ($line) {
            return in_array($line->branch_id, $this->selectedBranches);
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
        } elseif ($this->sortField === 'status') {
            $lines = $lines->sortBy('status');
        }

        if ($this->sortDirection === 'desc') {
            $lines = $lines->reverse();
        }

        // Enhance lines with usage data and color classes
        $this->agentLines = $lines->map(function ($line) {
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

        $this->agentLinesTotalBalance = $lines->sum('current_balance');

        // Load safes for selected branches
        $safes = collect($this->safeRepository->allWithBranch())->filter(function ($safe) {
            return in_array($safe['branch_id'] ?? $safe->branch_id, $this->selectedBranches);
        });

        $today = Carbon::today();
        $this->branchSafes = $safes->map(function ($safe) use ($today) {
            $safeId = $safe['id'] ?? $safe->id;
            $branchId = $safe['branch_id'] ?? $safe->branch_id;
            
            $cashTransactions = CashTransaction::where('safe_id', $safeId)
                ->whereDate('created_at', $today)
                ->count();
            $regularTransactions = Transaction::where('branch_id', $branchId)
                ->whereDate('created_at', $today)
                ->count();
            
            $todaysTransactions = $cashTransactions + $regularTransactions;
            
            return [
                'name' => $safe['name'] ?? $safe->name ?? '',
                'current_balance' => $safe['current_balance'] ?? $safe->current_balance ?? 0,
                'todays_transactions' => $todaysTransactions,
            ];
        });
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
            'searchedTransaction' => $this->searchedTransaction,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ]);
    }
} 