<?php

namespace App\Livewire\Reports;

use App\Application\UseCases\ListFilteredTransactions;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Customer;
use App\Domain\Entities\User;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\LineRepository;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TransactionsExport;
use Barryvdh\DomPDF\Facade\Pdf;

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
    private SafeRepository $safeRepository;
    private LineRepository $lineRepository;

    public function boot(
        ListFilteredTransactions $listFilteredTransactionsUseCase,
        SafeRepository $safeRepository,
        LineRepository $lineRepository
    )
    {
        $this->listFilteredTransactionsUseCase = $listFilteredTransactionsUseCase;
        $this->safeRepository = $safeRepository;
        $this->lineRepository = $lineRepository;
    }

    public function mount()
    {
        $this->authorize('view-reports');

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
            'agent_id' => $this->selectedUser,
            'branch_id' => $this->selectedBranch,
            'customer_name' => $this->selectedCustomer,
            'transaction_type' => $this->selectedTransactionType,
        ];

        $result = $this->listFilteredTransactionsUseCase->execute($filters);
        $this->transactions = $result['transactions'];
        $this->totalTransferred = $result['totals']['total_transferred'];
        $this->totalCommission = $result['totals']['total_commission'];
        $this->totalDeductions = $result['totals']['total_deductions'];
        $this->netProfits = $result['totals']['net_profit'];

        $safes = $this->safeRepository->all();
        $this->safeBalances = collect($safes)->groupBy('branch.name')
                                             ->mapWithKeys(function ($group, $branchName) {
                                                 return [$branchName => $group->sum('current_balance')];
                                             })->toArray();

        $lines = $this->lineRepository->all();
        $this->lineBalances = collect($lines)->groupBy('user.name')
                                             ->mapWithKeys(function ($group, $userName) {
                                                 return [$userName => $group->sum('current_balance')];
                                             })->toArray();
    }

    public function exportToExcel()
    {
        $this->authorize('view-reports');

        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'agent_id' => $this->selectedUser,
            'branch_id' => $this->selectedBranch,
            'customer_name' => $this->selectedCustomer,
            'transaction_type' => $this->selectedTransactionType,
        ];

        $result = $this->listFilteredTransactionsUseCase->execute($filters);
        $transactionsToExport = collect($result['transactions']);

        return Excel::download(new TransactionsExport($transactionsToExport), 'transactions_report.xlsx');
    }

    public function exportToPdf()
    {
        $this->authorize('view-reports');

        $filters = [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'agent_id' => $this->selectedUser,
            'branch_id' => $this->selectedBranch,
            'customer_name' => $this->selectedCustomer,
            'transaction_type' => $this->selectedTransactionType,
        ];

        $result = $this->listFilteredTransactionsUseCase->execute($filters);
        $transactionsToExport = collect($result['transactions']);

        $data = [
            'transactions' => $transactionsToExport,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalTransferred' => $this->totalTransferred,
            'totalCommission' => $this->totalCommission,
            'totalDeductions' => $this->totalDeductions,
            'netProfits' => $this->netProfits,
        ];

        $pdf = Pdf::loadView('reports.transactions_pdf', $data);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'transactions_report.pdf');
    }

    public function render()
    {
        $this->users = User::all();
        $this->branches = Branch::all();
        $this->customers = Customer::all();

        return view('livewire.reports.index', [
            'transactions' => $this->transactions,
            'totalTransferred' => $this->totalTransferred,
            'totalCommission' => $this->totalCommission,
            'totalDeductions' => $this->totalDeductions,
            'netProfits' => $this->netProfits,
            'safeBalances' => $this->safeBalances,
            'lineBalances' => $this->lineBalances,
            'users' => $this->users,
            'branches' => $this->branches,
            'customers' => $this->customers,
            'transactionTypes' => $this->transactionTypes,
        ]);
    }
}
