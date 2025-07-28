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
        $this->perPage += 10;
        $this->generateReport();
    }
    public $perPage = 10;
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
        $ordinary = Transaction::query();
        $cash = CashTransaction::query();

        // Date filter
        if ($this->startDate) {
            $ordinary->whereDate('transaction_date_time', '>=', $this->startDate);
            $cash->whereDate('transaction_date_time', '>=', $this->startDate);
        }
        if ($this->endDate) {
            $ordinary->whereDate('transaction_date_time', '<=', $this->endDate);
            $cash->whereDate('transaction_date_time', '<=', $this->endDate);
        }
        // Agent filter
        if ($this->selectedUser) {
            $ordinary->where('agent_id', $this->selectedUser);
            $cash->where('agent_id', $this->selectedUser);
        }
        // Branch filter
        if ($this->selectedBranch) {
            $ordinary->where('branch_id', $this->selectedBranch);
            $cash->whereHas('safe', function ($q) {
                $q->where('branch_id', $this->selectedBranch);
            });
        }
        // Customer name filter
        if ($this->selectedCustomer) {
            $ordinary->where('customer_name', 'like', '%' . $this->selectedCustomer . '%');
            $cash->where('customer_name', 'like', '%' . $this->selectedCustomer . '%');
        }
        // Customer code filter
        if ($this->selectedCustomerCode) {
            $ordinary->where('customer_code', 'like', '%' . $this->selectedCustomerCode . '%');
            $cash->where('customer_code', 'like', '%' . $this->selectedCustomerCode . '%');
        }
        // Type filter
        if ($this->selectedTransactionType) {
            $ordinary->where('transaction_type', $this->selectedTransactionType);
            $cash->where('transaction_type', $this->selectedTransactionType);
        }

        $ordinaryTxs = $ordinary->with(['agent', 'branch'])->take($this->perPage + 1)->get()->map(function ($tx) {
            return [
                'id' => $tx->id,
                'customer_name' => $tx->customer_name,
                'customer_code' => $tx->customer_code,
                'amount' => $tx->amount,
                'commission' => $tx->commission ?? 0,
                'deduction' => $tx->deduction ?? 0,
                'transaction_type' => $tx->transaction_type,
                'agent_name' => $tx->agent ? $tx->agent->name : '-',
                'status' => $tx->status,
                'transaction_date_time' => $tx->transaction_date_time,
                'reference_number' => $tx->reference_number,
                'branch_name' => $tx->branch ? $tx->branch->name : 'N/A',
                'source' => 'ordinary',
            ];
        });
        $cashTxs = $cash->with(['agent', 'safe.branch'])->take($this->perPage + 1)->get()->map(function ($tx) {
            return [
                'id' => $tx->id,
                'customer_name' => $tx->customer_name,
                'customer_code' => $tx->customer_code,
                'amount' => $tx->amount,
                'commission' => 0,
                'deduction' => 0,
                'transaction_type' => $tx->transaction_type,
                'agent_name' => $tx->agent ? $tx->agent->name : '-',
                'status' => $tx->status,
                'transaction_date_time' => $tx->transaction_date_time,
                'reference_number' => $tx->reference_number,
                'branch_name' => ($tx->safe && $tx->safe->branch) ? $tx->safe->branch->name : 'N/A',
                'source' => 'cash',
            ];
        });
        $all = collect($ordinaryTxs)->merge($cashTxs);

        $all = $all->sortBy(function ($tx) {
            return $tx[$this->sortField] ?? null;
        }, SORT_REGULAR, $this->sortDirection === 'desc');
        $all = $all->values();
        $this->totalCount = $all->count();
        $this->hasMore = $all->count() > $this->perPage;
        $this->transactions = $all->take($this->perPage)->all();


        // Financial summary
        $this->financialSummary = [
            'total_transfer' => $all->sum('amount'),
            'commission_earned' => $all->sum('commission'),
            'total_discounts' => $all->sum('deduction'),
            'net_profit' => $all->sum('commission') - $all->sum('deduction'),
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
        $export = new \App\Exports\AutoSizeTransactionsExport(collect($this->transactions));
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
            'transactions' => $this->transactions,
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
