<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Customer;
use App\Services\TotalsService;
use App\Infrastructure\Repositories\EloquentTransactionRepository;
use App\Exports\AutoSizeTransactionsExport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class Enhanced extends Component
{
    // Universal filter properties
    public $showLoading = false;
    public $mobileNumber = '';
    public $referenceNumber = '';
    public $amountFrom = '';
    public $amountTo = '';
    public $startDate = '';
    public $endDate = '';
    public $selectedBranches = [];
    public $selectedEmployee = '';
    public $customerSearch = '';

    // Report type and data
    public $reportType = 'transactions'; // transactions, employee, customer, branch
    public $transactions = [];
    public $totals = [];
    public $customers = [];
    public $employees = [];
    public $branches = [];

    // Table state
    public $sortField = 'transaction_date_time';
    public $sortDirection = 'desc';
    public $perPage = 50;
    public $totalCount = 0;
    public $hasMore = false;

    // Column filters
    public $filterCustomerName = '';
    public $filterTransactionType = '';
    public $filterStatus = '';
    public $filterEmployee = '';
    public $filterBranch = '';

    // Report-specific properties
    public $selectedCustomer = null;
    public $customerDetails = null;
    public $employeeDetails = null;
    public $branchDetails = [];

    // Services
    private TotalsService $totalsService;
    private EloquentTransactionRepository $transactionRepository;

    public function boot(TotalsService $totalsService, EloquentTransactionRepository $transactionRepository)
    {
        $this->totalsService = $totalsService;
        $this->transactionRepository = $transactionRepository;
    }

    public function mount()
    {
        // Authorization check
        Gate::authorize('view-all-reports');

        // Set default date range (last 30 days)
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');

        // Load reference data
        $this->loadReferenceData();

        // Generate initial report
        $this->generateReport();
    }

    public function loadReferenceData()
    {
        $user = Auth::user();

        // Load branches based on user permissions
        if (Gate::forUser($user)->allows('view-all-branches-data')) {
            $this->branches = Branch::all();
        } else {
            $this->branches = Branch::where('id', $user->branch_id)->get();
        }

        // Load employees based on user permissions
        if (Gate::forUser($user)->allows('view-all-branches-data')) {
            $this->employees = User::all();
        } else {
            $this->employees = User::where('branch_id', $user->branch_id)->get();
        }

        // Load customers
        $this->customers = Customer::all();
    }

    public function updatedReportType()
    {
        $this->resetFilters();
        $this->generateReport();
    }

    public function resetFilters()
    {
        $this->reset([
            'mobileNumber',
            'referenceNumber',
            'amountFrom',
            'amountTo',
            'selectedBranches',
            'selectedEmployee',
            'customerSearch',
            'filterCustomerName',
            'filterTransactionType',
            'filterStatus',
            'filterEmployee',
            'filterBranch'
        ]);
    }

    public function generateReport()
    {
        $filters = $this->buildFilters();

        switch ($this->reportType) {
            case 'employee':
                $this->generateEmployeeReport($filters);
                break;
            case 'customer':
                $this->generateCustomerReport($filters);
                break;
            case 'branch':
                $this->generateBranchReport($filters);
                break;
            default:
                $this->generateTransactionReport($filters);
        }
    }

    private function buildFilters(): array
    {
        $filters = [];

        // Universal filters
        if ($this->startDate) {
            $filters['start_date'] = $this->startDate;
        }
        if ($this->endDate) {
            $filters['end_date'] = $this->endDate;
        }
        if ($this->mobileNumber) {
            $filters['receiver_mobile_number'] = $this->mobileNumber;
        }
        if ($this->referenceNumber) {
            $filters['reference_number'] = $this->referenceNumber;
        }
        if ($this->amountFrom) {
            $filters['amount_from'] = $this->amountFrom;
        }
        if ($this->amountTo) {
            $filters['amount_to'] = $this->amountTo;
        }
        if (!empty($this->selectedBranches)) {
            $filters['branch_ids'] = $this->selectedBranches;
        }
        if ($this->selectedEmployee) {
            $filters['employee_ids'] = [$this->selectedEmployee];
        }

        // Column filters
        if ($this->filterCustomerName) {
            $filters['customer_name'] = $this->filterCustomerName;
        }
        if ($this->filterTransactionType) {
            $filters['transaction_type'] = $this->filterTransactionType;
        }
        if ($this->filterStatus) {
            $filters['status'] = $this->filterStatus;
        }

        // Sorting
        $filters['sortField'] = $this->sortField;
        $filters['sortDirection'] = $this->sortDirection;

        return $filters;
    }

    private function generateTransactionReport($filters)
    {
        // Fetch ordinary transactions
        $result = $this->transactionRepository->allUnified($filters);
        $ordinaryTransactions = collect($result['transactions']);

        // Fetch cash transactions with branch filtering
        $cashTransactions = CashTransaction::query()
            ->join('safes', 'cash_transactions.safe_id', '=', 'safes.id')
            ->when(!empty($filters['branch_ids']), function ($query) use ($filters) {
                $query->whereIn('safes.branch_id', $filters['branch_ids']);
            })
            ->when(!empty($filters['start_date']), function ($query) use ($filters) {
                $query->whereDate('cash_transactions.transaction_date_time', '>=', $filters['start_date']);
            })
            ->when(!empty($filters['end_date']), function ($query) use ($filters) {
                $query->whereDate('cash_transactions.transaction_date_time', '<=', $filters['end_date']);
            })
            ->select('cash_transactions.*')
            ->get();

        // Merge ordinary and cash transactions
        $allTransactions = $ordinaryTransactions->merge($cashTransactions);

        // Apply pagination
        $this->transactions = $allTransactions->take($this->perPage)->all();
        $this->totalCount = $allTransactions->count();
        $this->hasMore = $this->totalCount > $this->perPage;

        // Calculate totals
        $this->totals = $this->totalsService->calculateTotals($filters);
    }

    private function generateEmployeeReport($filters)
    {
        if (!$this->selectedEmployee) {
            $this->transactions = [];
            $this->totals = [];
            $this->employeeDetails = null;
            return;
        }

        // Get employee details
        $employee = User::find($this->selectedEmployee);
        if (!$employee) {
            $this->transactions = [];
            $this->totals = [];
            $this->employeeDetails = null;
            return;
        }

        $this->employeeDetails = [
            'name' => $employee->name,
            'id' => $employee->id,
            'phone' => $employee->phone_number ?? 'N/A',
            'branch' => $employee->branch->name ?? 'N/A',
            'employment_start_date' => $employee->employment_start_date,
        ];

        // If no date range specified, use employment start date
        if (!$this->startDate && $employee->employment_start_date) {
            $filters['start_date'] = $employee->employment_start_date;
        }

        // Generate report
        $this->generateTransactionReport($filters);
    }

    private function generateCustomerReport($filters)
    {
        if (!$this->customerSearch) {
            $this->transactions = [];
            $this->totals = [];
            $this->customerDetails = null;
            return;
        }

        // Find customer by code, name, or mobile
        $customer = Customer::where('customer_code', 'like', "%{$this->customerSearch}%")
            ->orWhere('name', 'like', "%{$this->customerSearch}%")
            ->orWhere('mobile_number', 'like', "%{$this->customerSearch}%")
            ->first();

        if ($customer) {
            $this->customerDetails = [
                'name' => $customer->name,
                'customer_code' => $customer->customer_code,
                'mobile_number' => $customer->mobile_number,
                'balance' => $customer->balance,
                'is_client' => $customer->is_client,
                'safe_balance' => $this->getCustomerSafeBalance($customer),
            ];

            // Filter by customer
            $filters['customer_code'] = $customer->customer_code;
        } else {
            // Try to find by transaction mobile numbers
            $filters['receiver_mobile_number'] = $this->customerSearch;
        }

        // Ensure cash transactions are filtered by mobile number
        if (isset($filters['receiver_mobile_number'])) {
            $filters['cash_transaction_mobile_number'] = $filters['receiver_mobile_number'];
        }

        // If no transactions match, return empty results
        $result = $this->transactionRepository->allUnified($filters);
        if (empty($result['transactions'])) {
            $this->transactions = [];
            $this->totals = [];
            return;
        }

        $this->generateTransactionReport($filters);
    }

    private function generateBranchReport($filters)
    {
        // Get branch details
        $branchIds = !empty($this->selectedBranches) ? $this->selectedBranches : $this->branches->pluck('id')->toArray();

        $this->branchDetails = [
            'safe_balances' => $this->totalsService->getSafeBalances($branchIds),
            'line_balances' => $this->totalsService->getLineBalances($branchIds),
        ];

        // Generate transaction report with branch expenses
        $this->generateTransactionReport($filters);
        $this->totals['total_expenses'] = $this->totalsService->calculateBranchExpenses($filters);
    }

    private function getCustomerSafeBalance($customer)
    {
        // Try to find customer safe (if linked by convention)
        $safe = \App\Models\Domain\Entities\Safe::where('type', 'client')
            ->where('name', 'like', "%{$customer->name}%")
            ->first();

        return $safe ? $safe->current_balance : null;
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

    public function loadMore()
    {
        $this->perPage += 50;
        $this->generateReport();
    }

    public function exportExcel()
    {
        $filters = $this->buildFilters();
        $result = $this->transactionRepository->allUnified($filters);
        $export = new AutoSizeTransactionsExport(collect($result['transactions']));

        $filename = $this->reportType . '_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function exportPdf()
    {
        $filters = $this->buildFilters();
        $result = $this->transactionRepository->allUnified($filters);

        $data = [
            'reportType' => $this->reportType,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'transactions' => $result['transactions'],
            'totals' => $this->totals,
            'customerDetails' => $this->customerDetails,
            'employeeDetails' => $this->employeeDetails,
            'branchDetails' => $this->branchDetails,
        ];

        $html = view('reports.enhanced_pdf', $data)->render();
        $mpdf = new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4', 'default_font' => 'dejavusans']);
        $mpdf->WriteHTML($html);

        $filename = $this->reportType . '_report_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        return response()->streamDownload(function () use ($mpdf) {
            echo $mpdf->Output('', 'S');
        }, $filename);
    }

    public function render()
    {
        return view('livewire.reports.enhanced', [
            'showEmployeeFilter' => in_array($this->reportType, ['transactions', 'employee']),
            'showCustomerFilter' => in_array($this->reportType, ['transactions', 'customer']),
            'showExpenses' => $this->reportType === 'branch',
        ]);
    }
}
