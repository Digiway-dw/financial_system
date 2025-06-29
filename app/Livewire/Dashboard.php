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

        switch ($user->role) {
            case 'admin':
                $data['totalUsers'] = count($this->userRepository->all());
                $data['totalBranches'] = count($this->branchRepository->all());
                $data['totalLines'] = count($this->lineRepository->all());
                $data['totalSafes'] = count($this->safeRepository->all());
                $data['totalCustomers'] = count($this->customerRepository->getAll());
                $data['totalTransactions'] = count($this->transactionRepository->all());

                $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
                $data['totalTransferred'] = $allTransactions['totals']['total_transferred'];
                $data['netProfits'] = $allTransactions['totals']['net_profit'];
                $data['pendingTransactionsCount'] = count($this->listPendingTransactionsUseCase->execute());
                break;

            case 'general_supervisor':
                $data['pendingTransactionsCount'] = count($this->listPendingTransactionsUseCase->execute());
                $allTransactions = $this->listFilteredTransactionsUseCase->execute([]);
                $data['totalTransferred'] = $allTransactions['totals']['total_transferred'];
                $data['netProfits'] = $allTransactions['totals']['net_profit'];
                break;

            case 'branch_manager':
                $userBranch = $user->branch;
                $data['branchName'] = $userBranch->name ?? 'N/A';
                
                $branchSafes = collect($this->safeRepository->all())->where('branch_id', $userBranch->id ?? null);
                $data['branchSafeBalance'] = $branchSafes->sum('current_balance');

                $pendingTransactions = $this->listPendingTransactionsUseCase->execute();
                $data['branchPendingTransactionsCount'] = collect($pendingTransactions)->where('branch_id', $userBranch->id ?? null)->count();
                
                $data['branchUsersCount'] = count($this->userRepository->getUsersByBranch($userBranch->id ?? null));
                break;

            case 'agent':
            case 'trainee':
                $agentLines = collect($this->lineRepository->all())->where('user_id', $user->id);
                $data['agentLines'] = [];
                foreach ($agentLines as $line) {
                    $data['agentLines'][] = $this->viewLineBalanceAndUsageUseCase->execute($line->id);
                }
                $data['agentTotalBalance'] = collect($data['agentLines'])->sum('current_balance');

                $agentTransactions = collect($this->transactionRepository->all())->where('agent_id', $user->id);
                $data['agentTotalTransferred'] = $agentTransactions->sum('amount');
                $data['agentPendingTransactionsCount'] = $agentTransactions->where('status', 'Pending')->count();
                break;
        }

        $dashboardView = 'livewire.dashboard.' . $user->role;

        return view($dashboardView, array_merge(['user' => $user], $data));
    }
}
