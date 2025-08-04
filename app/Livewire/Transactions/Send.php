<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Domain\Entities\Customer;
use App\Models\Domain\Entities\Line;
use App\Models\Domain\Entities\Safe;
use App\Application\UseCases\CreateTransaction;
use Illuminate\Support\Facades\Notification;
use App\Domain\Entities\User;
use App\Notifications\AdminNotification;

class Send extends Component
{
    // Client Information
    public $clientMobile = '';

    #[Validate('required|string|max:255')]
    public $clientName = '';

    #[Validate('nullable|in:male,female')]
    public $clientGender = '';

    public $clientCode = '';
    public $clientId = null;
    public $clientBalance = 0;

    // Receiver Information
    public $receiverMobile = '';

    // Transaction Details
    #[Validate('required|numeric|min:0.01')]
    public $amount = null;

    public $commission = null;

    #[Validate('nullable|numeric|min:0')]
    public $discount = null;

    #[Validate('required_if:discount,>0|string|min:3')]
    public $discountNotes = '';

    // Line Selection
    #[Validate('required|exists:lines,id')]
    public $selectedLineId = '';

    public $availableLines = [];

    // Branch Selection (for admin/supervisor)
    public $selectedBranchId = '';
    public $availableBranches = [];
    public $canSelectBranch = false;

    // Payment Options
    public $collectFromCustomerWallet = false;
    public $deductFromLineOnly = true;
    public $clientHasActiveWallet = false;

    // UI State
    public $clientSuggestions = [];
    public $lowBalanceWarning = '';
    public $successMessage = '';
    public $errorMessage = '';
    public $mobileFilledFromDropdown = false;

    // Form validation messages
    protected $messages = [
                    'amount.min' => 'Amount must be greater than 0.',
        'amount.min' => 'Minimum amount is 5 EGP.',
        'clientMobile.required' => 'Client mobile number is required.',
        'clientName.required' => 'Client name is required.',
        'receiverMobile.required' => 'Receiver mobile number is required.',
        'selectedLineId.required' => 'Please select an available line.',
        'selectedLineId.exists' => 'Selected line is not valid.',
        'discountNotes.required_if' => 'Discount notes are required when a discount is provided.',
        'discountNotes.min' => 'Discount notes must be at least 3 characters.',
    ];

    public function mount()
    {
        $this->initializeBranchSelection();
        $this->loadAvailableLines();
    }

    public function updatedClientMobile()
    {
        // Reset the dropdown flag when user manually types
        $this->mobileFilledFromDropdown = false;
        
        $this->searchClient();

        // If the mobile number does not match the selected client, clear selection
        if ($this->clientId) {
            $client = Customer::find($this->clientId);
            if (!$client || $client->mobile_number !== $this->clientMobile) {
                $this->clearClientSelection();
            }
        }
    }

    public function updatedAmount()
    {
        $this->calculateCommission();
        $this->checkLineBalance();
    }

    public function updatedDiscount()
    {
        if (empty($this->discount)) {
            $this->discount = null;
        }
        $this->calculateCommission();
    }

    public function updatedSelectedLineId()
    {
        $this->checkLineBalance();
    }

    public function updatedSelectedBranchId()
    {
        $this->loadAvailableLines();
        $this->selectedLineId = ''; // Reset line selection when branch changes
        $this->checkLineBalance();
    }

    public function updatedCollectFromCustomerWallet()
    {
        if ($this->collectFromCustomerWallet) {
            $this->deductFromLineOnly = false;
        } else {
            $this->deductFromLineOnly = !$this->collectFromCustomerWallet;
        }
        $this->checkLineBalance();
    }

    public function searchClient()
    {
        if (strlen($this->clientMobile) >= 2) {
            $clients = Customer::where(function ($query) {
                $query->where('mobile_number', 'like', '%' . $this->clientMobile . '%')
                    ->orWhere('customer_code', 'like', '%' . $this->clientMobile . '%')
                    ->orWhere('name', 'like', '%' . $this->clientMobile . '%');
            })
                ->limit(8)
                ->get(['id', 'name', 'mobile_number', 'customer_code', 'gender', 'balance']);

            $this->clientSuggestions = $clients->toArray();

            // Only auto-fill if there's an exact mobile number match AND only one result
            $exactMatch = $clients->where('mobile_number', $this->clientMobile)->first();
            if ($exactMatch && $clients->count() == 1) {
                $this->selectClient($exactMatch->id);
            }
        } else {
            $this->clientSuggestions = [];
        }
    }

    public function selectClient($clientId)
    {
        $client = Customer::find($clientId);
        if ($client) {
            $this->clientId = $client->id;
            $this->clientName = $client->name;
            $this->clientMobile = $client->mobile_number;
            $this->clientCode = $client->customer_code ?: $this->generateClientCode();
            $this->clientGender = $client->gender;
            $this->clientBalance = $client->balance;
            $this->clientHasActiveWallet = $client->is_client;
            $this->clientSuggestions = [];
            $this->mobileFilledFromDropdown = true;
        }
    }

    private function generateClientCode()
    {
        // Generate unique client code based on current timestamp and random number
        do {
            $code = 'C' . date('ym') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (Customer::where('customer_code', $code)->exists());

        return $code;
    }

    private function calculateCommission()
    {
        $amount = (float) $this->amount;
        // Commission calculation based on ranges
        if ($amount <= 0) {
            $this->commission = null;
            return;
        }
        
        $this->commission = $this->calculateBaseCommission($amount);
    }

    private function calculateBaseCommission($amount)
    {
        // Calculate commission based on ranges
        if ($amount <= 500) {
            return 5;
        } elseif ($amount <= 1000) {
            return 10;
        } elseif ($amount <= 1500) {
            return 15;
        } elseif ($amount <= 2000) {
            return 20;
        } else {
            // For amounts over 2000, add 5 EGP for each additional 500 EGP
            return 20 + (ceil(($amount - 2000) / 500) * 5);
        }
    }

    private function initializeBranchSelection()
    {
        $user = Auth::user();

        // Check if user can select branches (admin or supervisor)
        if ($user && ($user->hasRole('admin') || $user->hasRole('general_supervisor'))) {
            $this->canSelectBranch = true;
            $this->availableBranches = \App\Models\Domain\Entities\Branch::orderBy('name')->get(['id', 'name'])->toArray();
            // Set current user's branch as default
            $this->selectedBranchId = $user->branch_id;
        } else {
            $this->canSelectBranch = false;
            $this->selectedBranchId = $user->branch_id ?? '';
        }
    }

    private function loadAvailableLines()
    {
        $user = Auth::user();

        // Use selected branch if user can select branches, otherwise use user's branch
        $branchId = $this->canSelectBranch && $this->selectedBranchId ? $this->selectedBranchId : $user->branch_id;

        if ($user->hasRole('admin') || $user->hasRole('general_supervisor')) {
            // If branch is selected, filter by that branch, otherwise show all
            if ($this->selectedBranchId) {
                $linesQuery = Line::where('branch_id', $branchId)->where('status', 'active');
            } else {
                $linesQuery = Line::where('status', 'active');
            }
        } else {
            $linesQuery = Line::where('branch_id', $user->branch_id)->where('status', 'active');
        }
        $this->availableLines = $linesQuery
            ->get(['id', 'mobile_number', 'current_balance', 'network'])
            ->map(function ($line) {
                return [
                    'id' => $line->id,
                    'mobile_number' => $line->mobile_number,
                    'current_balance' => $line->current_balance,
                    'network' => $line->network,
                    'display' => $line->mobile_number . ' (' . number_format($line->current_balance, 0) . ' EGP) - ' . ucfirst($line->network),
                ];
            })
            ->toArray();
    }

    private function checkLineBalance()
    {
        $this->lowBalanceWarning = '';

        if (!$this->selectedLineId || !$this->amount) {
            return;
        }

        $line = collect($this->availableLines)->firstWhere('id', $this->selectedLineId);
        if (!$line) {
            return;
        }

        $amount = (float) $this->amount;
        $clientBalance = (float) $this->clientBalance;
        $requiredAmount = $amount;

        if ($this->collectFromCustomerWallet && $clientBalance > 0) {
            // If collecting from customer wallet, reduce required amount from line
            $requiredAmount = max(0, $amount - $clientBalance);
        }

        if ($line['current_balance'] < $requiredAmount) {
            $this->lowBalanceWarning = "Insufficient balance in selected line. Available: " .
                number_format($line['current_balance'], 2) . " EGP, Required: " .
                number_format($requiredAmount, 2) . " EGP. Please choose another line.";
        }
    }

    public function submitTransaction()
    {
        $this->discount = abs((float) $this->discount);
        
        // Custom validation for mobile numbers (only on submit, not while typing)
        $this->validateMobileNumbers();
        
        $this->validate();

        // Branch validation is handled in CreateTransaction use case based on the selected line's branch

        // Cast to proper types for arithmetic operations
        $amount = (float) $this->amount;
        $commission = (float) $this->commission;
        $discount = (float) $this->discount;
        $clientBalance = (float) $this->clientBalance;

        // Additional validation
        if ($amount <= 0) {
            $this->errorMessage = 'المبلغ غير صالح.';
            return;
        }

        // Check discount against base commission (before discount)
        $baseCommission = $this->calculateBaseCommission($amount);
        if ($discount > $baseCommission) {
            $this->errorMessage = 'الخصم لا يمكن أن يكون أكبر من العمولة.';
            return;
        }

        if ($this->collectFromCustomerWallet && $clientBalance < ($amount + $commission - $discount)) {
            $this->errorMessage = 'رصيد العميل غير كافي لهذه المعاملة.';
            return;
        }

        if ($this->lowBalanceWarning) {
            $this->errorMessage = 'يرجى حل مشكلات الرصيد قبل إرسال المعاملة.';
            return;
        }

        try {
            DB::transaction(function () use ($amount, $commission, $discount) {
                // Create or update client
                if (!$this->clientId) {
                    // Use selected branch if available, otherwise use user's branch
                    $branchId = $this->canSelectBranch && $this->selectedBranchId ? $this->selectedBranchId : Auth::user()->branch_id;

                    // Always generate a customer code if not set
                    $code = $this->clientCode ?: $this->generateClientCode();
                    $client = Customer::create([
                        'name' => $this->clientName,
                        'mobile_number' => $this->clientMobile,
                        'customer_code' => $code,
                        'gender' => $this->clientGender ?: 'male',
                        'balance' => 0,
                        'is_client' => false,
                        'agent_id' => Auth::id(),
                        'branch_id' => $branchId,
                    ]);
                    $this->clientId = $client->id;
                    $this->clientCode = $client->customer_code; // Always set clientCode from DB
                } else {
                    // Update existing client; if no code, generate and update
                    $client = Customer::find($this->clientId);
                    if ($client && empty($client->customer_code)) {
                        $client->customer_code = $this->generateClientCode();
                        $client->save();
                    }
                    $this->clientCode = $client ? $client->customer_code : $this->clientCode;
                    Customer::where('id', $this->clientId)->update([
                        'name' => $this->clientName,
                        'gender' => $this->clientGender,
                        'customer_code' => $this->clientCode,
                    ]);
                }

                // Get selected line for transaction
                $line = Line::find($this->selectedLineId);
                if (!$line) {
                    throw new \Exception('الخط غير موجود.');
                }

                $safe = $line->branch->safe;
                if (!$safe) {
                    // Try to find any safe for this branch as fallback
                    $safe = Safe::where('branch_id', $line->branch_id)->first();
                    if (!$safe) {
                        throw new \Exception('لا يوجد خزينة لهذا الفرع.');
                    }
                }

                // Create transaction using the CreateTransaction use case
                $transaction = app(CreateTransaction::class)->execute(
                    customerName: $this->clientName,
                    customerMobileNumber: $this->clientMobile,
                    customerCode: $this->clientCode,
                    amount: $amount,
                    commission: $commission,
                    deduction: $discount,
                    transactionType: 'Transfer',
                    agentId: Auth::id(),
                    lineId: $this->selectedLineId,
                    safeId: $safe->id,
                    isAbsoluteWithdrawal: false,
                    paymentMethod: $this->getPaymentMethod(),
                    gender: $this->clientGender ?: 'male',
                    isClient: false,
                    receiverMobileNumber: $this->receiverMobile,
                    discountNotes: $this->discountNotes,
                    notes: null
                );

                // IMPORTANT: Do NOT update daily_remaining or monthly_remaining for send transactions.
                // These fields must remain unchanged for this transaction type.

                // Notify admin if a discount was applied
                if (($this->discount ?? 0) > 0) {
                    $adminNotificationMessage = "تم إنشاء معاملة إرسال بخصم {$discount} EGP.\n"
                        . "Transaction Details:" . "\n"
                        . "Reference Number: {$transaction->reference_number}\n"
                        . "Client: {$this->clientName} ({$this->clientMobile})\n"
                        . "Amount: {$this->amount} EGP\n"
                        . "Commission: {$this->commission} EGP\n"
                        . "Discount: {$discount} EGP\n"
                        . "Receiver: {$this->receiverMobile}\n"
                        . "Line: {$this->selectedLineId}\n"
                        . "Branch: {$transaction->branch->name}\n"
                        . "Note: {$this->discountNotes}\n"
                        . "Transaction ID: {$transaction->id}";
                    $admins = User::role('admin')->get();
                    $supervisors = User::role('general_supervisor')->get();
                    $recipients = $admins->merge($supervisors)->unique('id');
                    Notification::send($recipients, new AdminNotification($adminNotificationMessage, route('transactions.edit', $transaction->reference_number)));
                }

                // Print receipt
                app(\App\Services\ReceiptPrinterService::class)->printReceipt($transaction, 'html');

                // Redirect to receipt page after successful transaction
                return redirect()->route('transactions.receipt', ['referenceNumber' => $transaction->reference_number]);
            });
        } catch (\Exception $e) {
            $this->errorMessage = 'فشل إنشاء المعاملة: ' . $e->getMessage();
        }
    }

    private function getPaymentMethod()
    {
        if ($this->collectFromCustomerWallet) {
            return 'client wallet';
        } else {
            return 'line balance';
        }
    }

    public function resetTransactionForm()
    {
        $this->reset([
            'clientMobile',
            'clientName',
            'clientGender',
            'clientCode',
            'clientId',
            'clientBalance',
            'clientHasActiveWallet',
            'receiverMobile',
            'amount',
            'commission',
            'discount',
            'discountNotes',
            'selectedLineId',
            'collectFromCustomerWallet',
            'deductFromLineOnly',
            'clientSuggestions',
            'lowBalanceWarning',
            'errorMessage',
            'mobileFilledFromDropdown'
        ]);
        $this->deductFromLineOnly = true;
        $this->loadAvailableLines();
    }

    public function clearClientSelection()
    {
        $this->reset([
            'clientId',
            'clientName',
            'clientMobile',
            'clientCode',
            'clientGender',
            'clientBalance',
            'clientSuggestions',
            'mobileFilledFromDropdown'
        ]);
    }

    /**
     * Validate mobile numbers only when submitting (not while typing)
     */
    private function validateMobileNumbers()
    {
        // Validate client mobile number
        if (empty($this->clientMobile)) {
            $this->addError('clientMobile', 'رقم هاتف العميل مطلوب.');
            return;
        }
        
        if (!preg_match('/^\d{11}$/', $this->clientMobile)) {
            $this->addError('clientMobile', 'رقم هاتف العميل يجب أن يكون 11 رقم.');
            return;
        }

        // Validate receiver mobile number
        if (empty($this->receiverMobile)) {
            $this->addError('receiverMobile', 'رقم هاتف المستلم مطلوب.');
            return;
        }
        
        if (!preg_match('/^\d{11}$/', $this->receiverMobile)) {
            $this->addError('receiverMobile', 'رقم هاتف المستلم يجب أن يكون 11 رقم.');
            return;
        }
    }

    public function render()
    {
        return view('livewire.transactions.send');
    }
}
