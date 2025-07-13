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
    )
    {
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
            } else {
                $lines = collect($this->lineRepository->all());
            }
            $data['branches'] = $allBranches;
            $data['selectedBranches'] = $selectedBranches ?? [];
            $data['agentLines'] = $lines;
            $data['agentLinesTotalBalance'] = $lines->sum('current_balance');
            $data['agentTotalBalance'] = $lines->sum('current_balance');
            $data['showAdminAgentToggle'] = true;
            $dashboardView = 'livewire.dashboard.agent';
        } else if ($user->hasRole('admin')) {
            $data['showAdminAgentToggle'] = true;
            $data['totalUsers'] = count($this->userRepository->all());
            $data['totalBranches'] = count($this->branchRepository->all());
            $data['totalLines'] = count($this->lineRepository->all());
            $data['totalSafes'] = count($this->safeRepository->all());
            $data['totalCustomers'] = \App\Models\Domain\Entities\Customer::count();
            $data['totalTransactions'] = count($this->transactionRepository->all());
            $data['totalSafeBalance'] = collect($this->safeRepository->all())->sum('current_balance');

            $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
            $data['totalTransferred'] = $allTransactions['totals']['total_transferred'];
            $data['netProfits'] = $allTransactions['totals']['net_profit'];
            // Count all transactions and cash transactions that require approval
            $pendingTransactionsCount = \App\Models\Domain\Entities\Transaction::where('status', 'Pending')->where('deduction', '>', 0)->count();
            $pendingCashCount = \App\Models\Domain\Entities\CashTransaction::where('status', 'pending')->where('transaction_type', 'Withdrawal')->count();
            $data['pendingTransactionsCount'] = $pendingTransactionsCount + $pendingCashCount;
            $dashboardView = 'livewire.dashboard.admin';
        } elseif ($user->hasRole('general_supervisor')) {
            $data['pendingTransactionsCount'] = count($this->listPendingTransactionsUseCase->execute());
            $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
            $data['totalTransferred'] = $allTransactions['totals']['total_transferred'];
            $data['netProfits'] = $allTransactions['totals']['net_profit'];
            $data['totalSafeBalance'] = collect($this->safeRepository->all())->sum('current_balance');
            $dashboardView = 'livewire.dashboard.general_supervisor';
        } elseif ($user->hasRole('branch_manager')) {
            $userBranch = $user->branch;
            $data['branchName'] = $userBranch->name ?? 'N/A';
            
            $branchSafes = collect($this->safeRepository->all())->where('branch_id', $userBranch->id ?? null);
            $data['branchSafeBalance'] = $branchSafes->sum('current_balance');

            $pendingTransactions = $this->listPendingTransactionsUseCase->execute();
            $data['branchPendingTransactionsCount'] = collect($pendingTransactions)->where('branch_id', $userBranch->id ?? null)->count();
            
            $branchUsers = collect($this->userRepository->getUsersByBranch($userBranch->id ?? null));
            $branchUsers = $branchUsers->filter(function ($u) {
                return !$u->hasRole('admin') && !$u->hasRole('general_supervisor');
            });
            $data['branchUsersCount'] = $branchUsers->count();
            // Fetch all lines for the branch
            $branchLines = \App\Models\Domain\Entities\Line::where('branch_id', $userBranch->id)->get();
            $data['branchLines'] = $branchLines;
            $data['branchLinesTotalBalance'] = $branchLines->sum('current_balance');
            $dashboardView = 'livewire.dashboard.branch_manager';
        } elseif ($user->hasRole('agent')) {
            $agentLines = collect($this->lineRepository->all())->where('branch_id', $user->branch_id ?? null);
            $data['agentLines'] = $agentLines;
            $data['agentLinesTotalBalance'] = $agentLines->sum('current_balance');
            $data['agentTotalBalance'] = $agentLines->sum('current_balance');

            $agentTransactions = collect($this->transactionRepository->all())->where('agent_id', $user->id);
            $data['agentTotalTransferred'] = $agentTransactions->sum('amount');
            $data['agentPendingTransactionsCount'] = $agentTransactions->where('status', 'Pending')->count();
            $dashboardView = 'livewire.dashboard.agent';
        } elseif ($user->hasRole('trainee')) {
            $agentLines = collect($this->lineRepository->all())->where('branch_id', $user->branch_id ?? null);
            $data['agentLines'] = $agentLines;
            $data['agentLinesTotalBalance'] = $agentLines->sum('current_balance');
            $data['agentTotalBalance'] = $agentLines->sum('current_balance');

            $agentTransactions = collect($this->transactionRepository->all())->where('agent_id', $user->id);
            $data['agentTotalTransferred'] = $agentTransactions->sum('amount');
            $data['agentPendingTransactionsCount'] = $agentTransactions->where('status', 'Pending')->count();
            $dashboardView = 'livewire.dashboard.trainee';
        }

        return view($dashboardView, array_merge(['user' => $user], $data));
    }
}
