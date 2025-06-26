<?php

namespace App\Livewire\Reports;

use App\Application\UseCases\ListFilteredTransactions;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Customer;
use App\Domain\Entities\User;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.app')]
class Index extends Component
{
    public $startDate;
    public $endDate;
    public $selectedUser;
    public $selectedBranch;
    public $selectedCustomer;
    public $selectedTransactionType = '';

    public $transactions = [];
    public $totalTransferred = 0;
    public $totalCommission = 0;
    public $totalDeductions = 0;
    public $netProfits = 0;
    public $safeBalances = [];
    public $lineBalances = [];

    public $users;
    public $branches;
    public $customers;
    public $transactionTypes = ['Transfer', 'Withdrawal', 'Deposit', 'Adjustment'];

    private ListFilteredTransactions $listFilteredTransactionsUseCase;

    public function boot(ListFilteredTransactions $listFilteredTransactionsUseCase)
    {
        $this->listFilteredTransactionsUseCase = $listFilteredTransactionsUseCase;
    }

    public function mount()
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
        $this->users = User::all();
        $this->branches = Branch::all();
        $this->customers = Customer::all();
        $this->generateReport();
    }

    public function generateReport()
    {
        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'user_id' => $this->selectedUser,
            'branch_id' => $this->selectedBranch,
            'customer_id' => $this->selectedCustomer,
            'transaction_type' => $this->selectedTransactionType,
        ];

        $this->transactions = $this->listFilteredTransactionsUseCase->execute($filters);
        $this->calculateReportMetrics();
    }

    private function calculateReportMetrics()
    {
        $this->totalTransferred = 0;
        $this->totalCommission = 0;
        $this->totalDeductions = 0;
        $this->netProfits = 0;

        foreach ($this->transactions as $transaction) {
            $this->totalTransferred += $transaction['amount'];
            $this->totalCommission += $transaction['commission'];
            $this->totalDeductions += $transaction['deduction'];
        }

        $this->netProfits = $this->totalCommission - $this->totalDeductions;

        // Safe and Line balances will require separate logic or relationships
        // For now, we'll just display them if available in the transaction or fetch separately
        // This might need more complex aggregation or direct queries to Safe/Line models.
        $this->safeBalances = []; // Placeholder
        $this->lineBalances = []; // Placeholder
    }

    public function render()
    {
        return view('livewire.reports.index', [
            'transactions' => $this->transactions,
            'totalTransferred' => $this->totalTransferred,
            'totalCommission' => $this->totalCommission,
            'totalDeductions' => $this->totalDeductions,
            'netProfits' => $this->netProfits,
            'safeBalances' => $this->safeBalances,
            'lineBalances' => $this->lineBalances,
        ]);
    }
}
