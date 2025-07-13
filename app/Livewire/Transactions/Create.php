<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\CreateTransaction;
use Livewire\Component;
use Livewire\Attributes\Validate;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Domain\Interfaces\CustomerRepository;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

class Create extends Component
{
    #[Validate('required|string|max:255')]
    public $customerName = '';

    #[Validate('required|string|max:20')]
    public $customerMobileNumber = '';

    #[Validate('required|string|max:20')]
    public $lineMobileNumber = '';

    #[Validate('nullable|string|max:255')]
    public $customerCode = '';

    #[Validate('required|string|in:Male,Female')]
    public $gender = 'Male';

    public $amount = 0;

    #[Validate('required|numeric|min:0')]
    public $commission = 0.00;

    #[Validate('required|numeric|min:0')]
    public $deduction = 0.00;

    #[Validate('required|string|in:Transfer,Withdrawal,Deposit,Adjustment')]
    public $transactionType = 'Transfer';

    public $agentName = '';

    public $status = 'Pending';

    #[Validate('required|exists:branches,id')]
    public $branchId = '';

    #[Validate('required|exists:lines,id')]
    public $lineId = '';

    #[Validate('required|exists:safes,id')]
    public $safeId = '';

    // New property for payment method
    #[Validate('required|string|in:client wallet,branch safe')]
    public $paymentMethod = 'branch safe';

    // New property for previously used destination numbers
    public $destinationNumbers = [];
    public $selectedDestinationNumber = '';

    // New property for isClient
    public $isClient = false;

    // New property for absolute withdrawal
    public $isAbsoluteWithdrawal = false;

    // New properties for receipt display
    public $showReceiptModal = false;
    public $completedTransaction = null;

    public $customerSearch = '';
    public $customerSearchResults = [];
    public $notes = '';

    public $searchCustomer = '';

    private ?CreateTransaction $createTransactionUseCase = null;

    public $branches;
    public $lines;
    public $safes;

    private ?CustomerRepository $customerRepository = null;

    public function updatedCustomerName($value)
    {
        if (!empty($value)) {
            $this->searchCustomers($value);
        } else {
            $this->customerSearchResults = [];
        }
    }

    public function updatedCustomerMobileNumber($value)
    {
        if (!empty($value)) {
            $this->searchCustomers($value);
        } else {
            $this->customerSearchResults = [];
        }
    }

    public function updatedSearchCustomer($value)
    {
        if (strlen($value) > 1) {
            $this->searchCustomers($value);
        } else {
            $this->customerSearchResults = [];
        }
    }

    public function searchCustomers($query)
    {
        if (!$this->customerRepository) {
            $this->customerSearchResults = [];
            return;
        }
        $this->customerSearchResults = $this->customerRepository->searchByNameOrMobile($query);
        logger(['search_query' => $query, 'results' => $this->customerSearchResults]);
    }

    public function selectCustomer($customerId)
    {
        $customer = $this->customerRepository->findById($customerId);
        if ($customer) {
            $this->customerName = $customer->name;
            $this->customerMobileNumber = $customer->mobile_number;
            $this->customerCode = $customer->customer_code;
            $this->selectedDestinationNumber = $customer->mobile_number;
            $this->customerSearchResults = [];
            $this->searchCustomer = '';
        }
    }

    public function fillCustomerFields($customerId)
    {
        $customer = $this->customerRepository->findById($customerId);
        if ($customer) {
            $this->customerName = $customer->name;
            $this->customerMobileNumber = $customer->mobile_number;
            $this->customerCode = $customer->customer_code;
            $this->gender = $customer->gender ?? 'Male';
            // Add more fields as needed
            $this->customerSearchResults = [];
        }
    }

    public function updatedDeduction($value)
    {
        if ($value > 0) {
            $this->notes = '';
        }
        // Ensure deduction does not exceed commission
        if ($value > $this->commission) {
            $this->deduction = $this->commission;
            session()->flash('error', 'Deduction cannot exceed commission.');
        }
    }

    public function updatedCustomerCode($value)
    {
        if (!empty($value)) {
            $customer = $this->customerRepository->findByCustomerCode($value);
            if ($customer) {
                $this->customerName = $customer->name;
                $this->customerMobileNumber = $customer->mobile_number;
                // customerCode is already set by the input field
            }
        }
    }

    public function updatedSelectedDestinationNumber($value)
    {
        if (!empty($value)) {
            $customer = $this->customerRepository->findByMobileNumber($value);
            if ($customer) {
                $this->customerMobileNumber = $customer->mobile_number;
                $this->customerName = $customer->name;
                $this->customerCode = $customer->customer_code;
            }
        } else {
            // Clear fields if no destination number is selected
            $this->customerMobileNumber = '';
            $this->customerName = '';
            $this->customerCode = '';
        }
    }

    public function updatedTransactionType()
    {
        // Reset absolute withdrawal flag if transaction type changes from Withdrawal
        if ($this->transactionType !== 'Withdrawal') {
            $this->isAbsoluteWithdrawal = false;
        }
    }

    /**
     * Create a new transaction with proper validation and authorization
     */
    public function createTransaction()
    {
        $this->validate();

        // If this transaction is for a line and needs admin approval, check line limits before saving
        $needsApproval = $this->deduction > 0 || $this->transactionType === 'Receive' || $this->transactionType === 'Deposit';
        if ($this->lineId && $needsApproval) {
            $line = \App\Models\Domain\Entities\Line::find($this->lineId);
            if ($line) {
                $monthStart = now()->startOfMonth();
                $monthEnd = now()->endOfMonth();
                $todayStart = now()->startOfDay();
                $todayEnd = now()->endOfDay();
                $monthlyReceived = \App\Models\Domain\Entities\Transaction::where('line_id', $line->id)
                    ->whereIn('transaction_type', ['Deposit', 'Receive'])
                    ->whereBetween('transaction_date_time', [$monthStart, $monthEnd])
                    ->sum('amount');
                $monthlyLimit = $line->monthly_limit;
                $startingBalance = $line->starting_balance ?? 0;
                $maxAllowedMonthly = ($monthlyLimit !== null) ? ($monthlyLimit - $startingBalance) : null;
                if ($maxAllowedMonthly !== null && ($monthlyReceived + $this->amount) > $maxAllowedMonthly) {
                    $line->status = 'frozen';
                    $line->save();
                    session()->flash('error', 'Transaction exceeds the allowed monthly receive limit for this line. The line has been frozen until the start of next month.');
                    return;
                }
                $dailyReceived = \App\Models\Domain\Entities\Transaction::where('line_id', $line->id)
                    ->whereIn('transaction_type', ['Deposit', 'Receive'])
                    ->whereBetween('transaction_date_time', [$todayStart, $todayEnd])
                    ->sum('amount');
                $dailyLimit = $line->daily_limit;
                $dailyStartingBalance = $line->daily_starting_balance ?? 0;
                $maxAllowedDaily = ($dailyLimit !== null) ? ($dailyLimit - $dailyStartingBalance) : null;
                if ($maxAllowedDaily !== null && ($dailyReceived + $this->amount) > $maxAllowedDaily) {
                    $line->status = 'frozen';
                    $line->save();
                    session()->flash('error', 'Transaction exceeds the allowed daily receive limit for this line. The line has been frozen until the end of the day.');
                    return;
                }
            }
        }

        // Ensure isAbsoluteWithdrawal is only true for Admin with permission and Withdrawal type
        if (!Gate::allows('perform-unrestricted-withdrawal') || $this->transactionType !== 'Withdrawal') {
            $this->isAbsoluteWithdrawal = false;
        }

        try {
            $createdTransaction = $this->createTransactionUseCase->execute(
                $this->customerName,
                $this->customerMobileNumber,
                $this->customerCode,
                (float) $this->amount,
                (float) $this->commission,
                (float) $this->deduction,
                $this->transactionType,
                Auth::user()->id, // agentId
                $this->lineId,
                $this->safeId,
                $this->isAbsoluteWithdrawal,
                $this->paymentMethod,
                $this->gender,
                $this->isClient
            );

            // If we reach here, the transaction was created successfully
            if ($this->deduction > 0) {
                $adminNotificationMessage = "A transaction was created with a deduction of " . $this->deduction . " EGP. Note: " . $this->notes . ". Transaction ID: " . $createdTransaction->id;
                $admins = \App\Domain\Entities\User::role('admin')->get();
                Notification::send($admins, new AdminNotification($adminNotificationMessage, route('transactions.edit', $createdTransaction->id)));
                session()->flash('message', 'Transaction submitted for admin approval due to discount applied.');
                $this->reset(['customerName', 'customerMobileNumber', 'lineMobileNumber', 'customerCode', 'amount', 'commission', 'deduction', 'transactionType', 'branchId', 'lineId', 'safeId', 'isAbsoluteWithdrawal', 'paymentMethod', 'gender', 'isClient']);
                $this->calculateCommission();
                return redirect()->route('transactions.waiting-approval', ['transactionId' => $createdTransaction->id]);
            }

            $this->completedTransaction = $createdTransaction;
            $this->showReceiptModal = true;
            app(\App\Services\ReceiptPrinterService::class)->printReceipt($createdTransaction, 'html');
            session()->flash('message', 'Transaction created successfully.');
            $this->reset(['customerName', 'customerMobileNumber', 'lineMobileNumber', 'customerCode', 'amount', 'commission', 'deduction', 'transactionType', 'branchId', 'lineId', 'safeId', 'isAbsoluteWithdrawal', 'paymentMethod', 'gender', 'isClient']);
            $this->calculateCommission();
        } catch (\Exception $e) {
            // If any exception occurs (such as exceeding line limit), show error and do NOT save or send for approval
            session()->flash('error', $e->getMessage());
            return;
        }
    }

    public function closeReceiptModal()
    {
        $this->showReceiptModal = false;
        $this->completedTransaction = null;
    }

    public function render()
    {
        $this->branches = Branch::all();
        $this->lines = Line::all();
        $this->safes = Safe::all();

        return view('livewire.transactions.create', [
            'branches' => $this->branches,
            'lines' => $this->lines,
            'safes' => $this->safes,
        ]);
    }

    public function rules()
    {
        $rules = [
            'customerMobileNumber' => 'required|string|max:20',
            'amount' => [
                'required',
                'integer',
                'min:5',
                function ($attribute, $value, $fail) {
                    if ($value % 5 !== 0) {
                        $fail('The ' . $attribute . ' must be a multiple of 5.');
                    }
                },
            ],
            'deduction' => 'nullable|numeric|min:0',
        ];
        if ($this->deduction > 0) {
            $rules['notes'] = 'required|string|min:2';
        }
        return $rules;
    }

    public function boot(CreateTransaction $createTransactionUseCase, CustomerRepository $customerRepository)
    {
        $this->createTransactionUseCase = $createTransactionUseCase;
        $this->customerRepository = $customerRepository;
        // Populate destination numbers after customerRepository is set
        $this->destinationNumbers = $this->customerRepository->getAll();
    }

    public function mount()
    {
        Gate::authorize('send-transfer'); // Allow agents and trainees to access this page
        $this->agentName = Auth::user()->name; // Automatically set agent name
        $this->branchId = Auth::user()->branch_id; // Automatically set agent branch
        // Auto-calculate commission based on amount
        $this->calculateCommission();
    }

    public function updatedAmount()
    {
        $this->calculateCommission();
    }

    private function calculateCommission()
    {
        // Example: 5 EGP per 500 EGP, with a minimum of 5 EGP
        if ($this->amount > 0) {
            $calculatedCommission = (floor($this->amount / 500) * 5);
            $this->commission = max(5, $calculatedCommission); // Ensure minimum 5 EGP commission
        } else {
            $this->commission = 0.00;
        }
    }
}
