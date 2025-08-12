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
use App\Models\Domain\Entities\Safe; // Correct import for Safe model
use App\Models\Domain\Entities\Line;

class Enhanced extends Component
{
    // Universal filter properties
    public $showLoading = false;
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
    public $filterMobileNumber = ''; // New mobile number filter

    // Report-specific properties
    public $selectedCustomer = null;
    public $customerDetails = null;
    public $employeeDetails = null;
    public $branchDetails = [];

    // New properties for line filtering
    public $selectedLine = ''; // New property for line filter
    public $lines = []; // Store lines for dropdown

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
            $this->branches = Branch::where('is_active', true)->get();
        } else {
            $this->branches = Branch::where('id', $user->branch_id)->where('is_active', true)->get();
        }

        // Load all lines for the dropdown
        $this->lines = Line::all()->pluck('mobile_number', 'id')->toArray();

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
            'filterBranch',
            'selectedLine' // Reset selected line
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
        if ($this->selectedLine) {
            $filters['transfer_line'] = $this->selectedLine;
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
        if ($this->filterMobileNumber) {
            $filters['receiver_mobile_number'] = $this->filterMobileNumber;
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

        $allTransactions = $ordinaryTransactions;

        // Only include cash transactions if not filtering by mobile number or line
        if (empty($filters['receiver_mobile_number']) && empty($filters['transfer_line'])) {
            $cashTransactions = CashTransaction::query()
                ->with(['safe.branch'])
                ->get();
            
            // Set branch_name and branch_id for all cash transactions BEFORE filtering
            foreach ($cashTransactions as $transaction) {
                $branchName = null;
                $branchId = null;
                
                // First try safe->branch relationship
                if ($transaction->safe && $transaction->safe->branch) {
                    $branchName = $transaction->safe->branch->name;
                    $branchId = $transaction->safe->branch->id;
                }
                // Then try destination_branch_id
                elseif ($transaction->destination_branch_id) {
                    $branch = \App\Models\Domain\Entities\Branch::find($transaction->destination_branch_id);
                    if ($branch) {
                        $branchName = $branch->name;
                        $branchId = $transaction->destination_branch_id;
                    }
                }
                // Fallback: try safe->branch_id if safe exists but no branch loaded
                elseif ($transaction->safe && $transaction->safe->branch_id) {
                    $branch = \App\Models\Domain\Entities\Branch::find($transaction->safe->branch_id);
                    if ($branch) {
                        $branchName = $branch->name;
                        $branchId = $transaction->safe->branch_id;
                    }
                }
                
                $transaction->branch_name = $branchName ?? 'N/A';
                $transaction->branch_id = $branchId;
                
                // Ensure cash transactions have a source field for export consistency
                if (!isset($transaction->source)) {
                    $transaction->source = 'cash_transaction';
                }
            }
            
            // Now filter cash transactions by selected branch AFTER setting branch names
            if (!empty($filters['branch_ids'])) {
                $branchIds = $filters['branch_ids'];
                $cashTransactions = $cashTransactions->filter(function ($transaction) use ($branchIds) {
                    return $transaction->branch_id && in_array($transaction->branch_id, $branchIds);
                });
            }
            
            // Convert cash transactions to array format to match ordinary transactions
            $cashTransactionsArray = $cashTransactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'reference_number' => $transaction->reference_number,
                    'customer_name' => $transaction->customer_name,
                    'customer_code' => $transaction->customer_code,
                    'amount' => $transaction->amount,
                    'commission' => 0, // Cash transactions don't have commission
                    'deduction' => 0, // Cash transactions don't have deduction
                    'transaction_type' => $transaction->transaction_type,
                    'status' => $transaction->status,
                    'agent_name' => $transaction->agent ? $transaction->agent->name : '',
                    'transaction_date_time' => $transaction->transaction_date_time,
                    'branch_name' => $transaction->branch_name,
                    'branch_id' => $transaction->branch_id,
                    'source' => 'cash_transaction',
                ];
            });
            
            // Remove duplicates by reference_number
            $allTransactions = $ordinaryTransactions->merge($cashTransactionsArray)->unique('reference_number');
        }

        // Apply pagination
        $paginatedTransactions = $allTransactions->take($this->perPage)->all();
        $this->transactions = $paginatedTransactions;
        $this->totalCount = $allTransactions->count();
        $this->hasMore = $this->totalCount > $this->perPage;

        // Calculate totals using only the displayed transactions
        $displayed = collect($paginatedTransactions);
        $this->totals = [
            'total_turnover' => $displayed->sum('amount'),
            'total_commissions' => $displayed->sum('commission'),
            'total_deductions' => $displayed->sum('deduction'),
            'net_profit' => $displayed->sum('commission') + $displayed->where('profit_contribution', '<', 0)->sum('profit_contribution'),
            'transactions_count' => $displayed->count(),
        ];
    }

    private function generateEmployeeReport($filters)
    {
        // If no employee selected, show all transactions
        if (!$this->selectedEmployee) {
            $this->employeeDetails = null;
            $this->generateTransactionReport($filters);
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
        // If no customer search, show all transactions
        if (!$this->customerSearch) {
            $this->customerDetails = null;
            $this->generateTransactionReport($filters);
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
        // Use the same transactions as displayed in the UI, with branch_name already set
        $export = new AutoSizeTransactionsExport(collect($this->transactions));
        $filename = $this->reportType . '_report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download($export, $filename);
    }

    public function exportPdf()
    {
        // Use the same transactions as displayed in the UI, with branch_name already set
        $data = [
            'reportType' => $this->reportType,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'transactions' => $this->transactions,
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
            'lines' => $this->lines,
            'showEmployeeFilter' => in_array($this->reportType, ['transactions', 'employee']),
            'showCustomerFilter' => in_array($this->reportType, ['transactions', 'customer']),
            'showExpenses' => $this->reportType === 'branch',
        ]);
    }
}
