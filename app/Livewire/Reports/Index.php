<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Customer;


class Index extends Component
{
    public $totalCount = 0;

    public function loadMore()
    {
        $this->perPage += 30;
        $this->generateReport();
    }
    public $perPage = 30;
    public $hasMore = false;
    public $startDate;
    public $endDate;
    public $selectedUser;
    public $selectedBranch;
    public $selectedCustomer;
    public $selectedTransactionType = '';
    public $sortField = 'transaction_date_time';
    public $sortDirection = 'desc';
    public $transactions = [];
    public $users = [];
    public $branches = [];
    public $customers = [];
    public $transactionTypes = ['Transfer', 'Withdrawal', 'Deposit', 'Adjustment'];
    public $selectedCustomerCode;
    public $financialSummary = [];
    public $safeBalances = [];
    public $lineBalances = [];
    public $customerBalances = [];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->users = User::all();
        $this->branches = Branch::all();
        $this->customers = Customer::all();
        $this->generateReport();
    }

    public function generateReport()
    {
        // Use the unified transaction repository for accurate calculations
        $repository = new \App\Infrastructure\Repositories\EloquentTransactionRepository();
        
        // Build filters for the repository
        $filters = [];
        
        // Date filter
        if ($this->startDate) {
            $filters['start_date'] = $this->startDate;
        }
        if ($this->endDate) {
            $filters['end_date'] = $this->endDate;
        }
        
        // Agent filter
        if ($this->selectedUser) {
            $filters['agent_id'] = $this->selectedUser;
        }
        
        // Branch filter
        if ($this->selectedBranch) {
            $filters['branch_id'] = $this->selectedBranch;
        }
        
        // Customer name filter
        if ($this->selectedCustomer) {
            $filters['customer_name'] = $this->selectedCustomer;
        }
        
        // Customer code filter
        if ($this->selectedCustomerCode) {
            $filters['customer_code'] = $this->selectedCustomerCode;
        }
        
        // Type filter
        if ($this->selectedTransactionType) {
            $filters['transaction_type'] = $this->selectedTransactionType;
        }
        
        // Sorting
        $filters['sortField'] = $this->sortField;
        $filters['sortDirection'] = $this->sortDirection;
        
        // Get unified transactions with proper calculations
        $result = $repository->allUnified($filters);
        $all = collect($result['transactions']);
        
        // Apply pagination
        $this->totalCount = $all->count();
        $this->hasMore = $all->count() > $this->perPage;
        $this->transactions = $all->take($this->perPage)->all();

        // Use the accurate financial summary from the repository
        $this->financialSummary = [
            'total_transfer' => $result['totals']['total_transferred'],
            'commission_earned' => $result['totals']['total_commission'],
            'total_discounts' => $result['totals']['total_deductions'],
            'net_profit' => $result['totals']['net_profit'],
        ];

        // Safe balances by branch
        $this->safeBalances = \App\Models\Domain\Entities\Safe::with('branch')->get()->groupBy('branch_id')->map(function ($safes) {
            return [
                'branch' => $safes->first()->branch->name ?? '-',
                'balance' => $safes->sum('current_balance'),
            ];
        })->values()->all();

        // Line balances by branch
        $this->lineBalances = \App\Models\Domain\Entities\Line::with('branch')->get()->groupBy('branch_id')->map(function ($lines) {
            return [
                'branch' => $lines->first()->branch->name ?? '-',
                'balance' => $lines->sum('current_balance'),
            ];
        })->values()->all();

        // Customer balances
        $this->customerBalances = \App\Models\Domain\Entities\Customer::where('is_client', true)->get()->map(function ($customer) {
            return [
                'customer' => $customer->name,
                'balance' => $customer->balance,
            ];
        })->all();
    }



    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->generateReport();
    }

    public function render()
    {
        return view('livewire.reports.index', [
            'transactions' => $this->transactions,
            'users' => $this->users,
            'branches' => $this->branches,
            'customers' => $this->customers,
            'transactionTypes' => $this->transactionTypes,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'selectedUser' => $this->selectedUser,
            'selectedBranch' => $this->selectedBranch,
            'selectedCustomer' => $this->selectedCustomer,
            'selectedTransactionType' => $this->selectedTransactionType,
            'hasMore' => $this->hasMore,
            'totalCount' => $this->totalCount,
        ]);
    }

    public function exportExcel()
    {
        // Use the unified transaction repository for accurate calculations
        $repository = new \App\Infrastructure\Repositories\EloquentTransactionRepository();
        
        // Build filters for the repository
        $filters = [];
        
        // Date filter
        if ($this->startDate) {
            $filters['start_date'] = $this->startDate;
        }
        if ($this->endDate) {
            $filters['end_date'] = $this->endDate;
        }
        
        // Agent filter
        if ($this->selectedUser) {
            $filters['agent_id'] = $this->selectedUser;
        }
        
        // Branch filter
        if ($this->selectedBranch) {
            $filters['branch_id'] = $this->selectedBranch;
        }
        
        // Customer name filter
        if ($this->selectedCustomer) {
            $filters['customer_name'] = $this->selectedCustomer;
        }
        
        // Customer code filter
        if ($this->selectedCustomerCode) {
            $filters['customer_code'] = $this->selectedCustomerCode;
        }
        
        // Type filter
        if ($this->selectedTransactionType) {
            $filters['transaction_type'] = $this->selectedTransactionType;
        }
        
        // Sorting
        $filters['sortField'] = $this->sortField;
        $filters['sortDirection'] = $this->sortDirection;
        
        // Get unified transactions with proper calculations
        $result = $repository->allUnified($filters);
        $all = collect($result['transactions']);

        $export = new \App\Exports\AutoSizeTransactionsExport($all);
        return \Maatwebsite\Excel\Facades\Excel::download($export, 'transactions_report.xlsx');
    }

    public function exportSummaryPdf()
    {
        $summary = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalTransferred' => $this->financialSummary['total_transfer'] ?? 0,
            'totalCommission' => $this->financialSummary['commission_earned'] ?? 0,
            'totalDeductions' => $this->financialSummary['total_discounts'] ?? 0,
            'netProfits' => $this->financialSummary['net_profit'] ?? 0,
            'financialSummary' => $this->financialSummary,
            'customerBalances' => $this->customerBalances,
            'safeBalances' => $this->safeBalances,
            'lineBalances' => $this->lineBalances,
        ];
        $html = view('reports.summary_pdf', $summary)->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->WriteHTML($html);
        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'system_summary_report.pdf');
    }

    public function exportAllPdf()
    {
        // Use the unified transaction repository for accurate calculations
        $repository = new \App\Infrastructure\Repositories\EloquentTransactionRepository();
        
        // Build filters for the repository
        $filters = [];
        
        // Date filter
        if ($this->startDate) {
            $filters['start_date'] = $this->startDate;
        }
        if ($this->endDate) {
            $filters['end_date'] = $this->endDate;
        }
        
        // Agent filter
        if ($this->selectedUser) {
            $filters['agent_id'] = $this->selectedUser;
        }
        
        // Branch filter
        if ($this->selectedBranch) {
            $filters['branch_id'] = $this->selectedBranch;
        }
        
        // Customer name filter
        if ($this->selectedCustomer) {
            $filters['customer_name'] = $this->selectedCustomer;
        }
        
        // Customer code filter
        if ($this->selectedCustomerCode) {
            $filters['customer_code'] = $this->selectedCustomerCode;
        }
        
        // Type filter
        if ($this->selectedTransactionType) {
            $filters['transaction_type'] = $this->selectedTransactionType;
        }
        
        // Sorting
        $filters['sortField'] = $this->sortField;
        $filters['sortDirection'] = $this->sortDirection;
        
        // Get unified transactions with proper calculations
        $result = $repository->allUnified($filters);
        $all = collect($result['transactions']);

        $summary = [
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalTransferred' => $result['totals']['total_transferred'],
            'totalCommission' => $result['totals']['total_commission'],
            'totalDeductions' => $result['totals']['total_deductions'],
            'netProfits' => $result['totals']['net_profit'],
            'financialSummary' => $this->financialSummary,
            'customerBalances' => $this->customerBalances,
            'safeBalances' => $this->safeBalances,
            'lineBalances' => $this->lineBalances,
            'transactions' => $all->all(),
        ];
        $html = view('reports.all_pdf', $summary)->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->WriteHTML($html);
        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'full_report.pdf');
    }

    public function exportPdf()
    {
        $summary = [
            'تاريخ البدء' => $this->startDate,
            'تاريخ النهاية' => $this->endDate,
            'إجمالي التحويلات' => $this->financialSummary['total_transfer'] ?? 0,
            'إجمالي العمولات' => $this->financialSummary['commission_earned'] ?? 0,
            'إجمالي الخصومات' => $this->financialSummary['total_discounts'] ?? 0,
            'صافي الربح' => $this->financialSummary['net_profit'] ?? 0,
            // Add English keys for Blade compatibility
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'totalTransferred' => $this->financialSummary['total_transfer'] ?? 0,
            'totalCommission' => $this->financialSummary['commission_earned'] ?? 0,
            'totalDeductions' => $this->financialSummary['total_discounts'] ?? 0,
            'netProfits' => $this->financialSummary['net_profit'] ?? 0,
        ];
        $html = view('reports.transactions_pdf', array_merge([
            'transactions' => $this->transactions
        ], $summary))->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->WriteHTML($html);
        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, 'transactions_report.pdf');
    }
}
