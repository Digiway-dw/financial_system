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
use App\Notifications\AdminNotification;

class Receive extends Component
{
    // Client Information
    public $clientMobile = '';

    #[Validate('required|string|max:255')]
    public $clientName = '';

    #[Validate('nullable|in:male,female')]
    public $clientGender = '';

    public $clientCode = '';
    public $clientId = null;
    public $clientBalance = null;

    // Sender Information
    public $senderMobile = '';

    // Transaction Details
    #[Validate('required|numeric|min:0.01')]
    public $amount = null;

    public $commission = null;

    #[Validate('nullable|numeric|min:0')]
    public $discount = null;

    #[Validate('required_if:discount,>0')]
    public $discountNotes = '';

    // Line Selection
    #[Validate('required|exists:lines,id')]
    public $selectedLineId = '';

    public $availableLines = [];

    // Branch Selection (for admin/supervisor)
    public $selectedBranchId = '';
    public $availableBranches = [];
    public $canSelectBranch = false;

    // UI State
    public $clientSuggestions = [];
    public $safeBalanceWarning = '';
    public $successMessage = '';
    public $errorMessage = '';
    public $mobileFilledFromDropdown = false;

    // Form validation messages
    protected $messages = [
                    'amount.min' => 'Amount must be greater than 0.',
        'amount.min' => 'Minimum amount is 5 EGP.',
        'clientMobile.required' => 'Client mobile number is required.',
        'clientName.required' => 'Client name is required.',
        'senderMobile.required' => 'Sender mobile number is required.',
        'selectedLineId.required' => 'Please select an available line.',
        'selectedLineId.exists' => 'Selected line is not valid.',
        'discountNotes.required_if' => 'Discount notes are required when discount is provided.',
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
        $this->checkSafeBalance();
    }

    public function updatedDiscount()
    {
        if (empty($this->discount)) {
            $this->discount = null;
        }
        $this->calculateCommission();
        $this->checkSafeBalance();
    }

    public function updatedSelectedLineId()
    {
        $this->checkSafeBalance();
    }

    public function updatedSelectedBranchId()
    {
        $this->loadAvailableLines();
        $this->selectedLineId = ''; // Reset line selection when branch changes
        $this->checkSafeBalance();
    }

    private function searchClient()
    {
        if (strlen($this->clientMobile) >= 3) {
            $clients = Customer::where('mobile_number', 'like', '%' . $this->clientMobile . '%')
                ->limit(5)
                ->get(['id', 'name', 'mobile_number', 'customer_code', 'gender', 'balance']);

            $this->clientSuggestions = $clients->toArray();

            // Auto-fill if exact match
            $exactMatch = $clients->where('mobile_number', $this->clientMobile)->first();
            if ($exactMatch) {
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
        if ($amount <= 0) {
            $this->commission = 0;
            return;
        }
        // Commission calculation based on ranges
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

    private function checkSafeBalance()
    {
        $this->safeBalanceWarning = '';

        if (!$this->selectedLineId || !$this->amount) {
            return;
        }

        // Get the line to find the associated branch/safe
        $line = Line::find($this->selectedLineId);
        if (!$line) {
            return;
        }

        $safe = $line->branch->safe;
        if (!$safe) {
            // Try to find any safe for this branch as fallback
            $safe = Safe::where('branch_id', $line->branch_id)->first();
            if (!$safe) {
                $this->safeBalanceWarning = "No safe found for this branch.";
                return;
            }
        }

        $amount = (float) $this->amount;
        $commission = (float) $this->commission;

        // For receive transactions, we need to deduct (amount - commission) from safe
        $requiredFromSafe = $amount - $commission;

        if ($safe->current_balance < $requiredFromSafe) {
            $this->safeBalanceWarning = "Insufficient balance in safe. Available: " .
                number_format($safe->current_balance, 2) . " EGP, Required: " .
                number_format($requiredFromSafe, 2) . " EGP.";
        }
    }

    private function notifyAdmin($transaction)
    {
        try {
            $discountDisplay = $this->discount ?? 0;
            $adminNotificationMessage = "تم إنشاء معاملة إستلام بخصم {$discountDisplay} EGP.\n"
                . "تفاصيل المعاملة:" . "\n"
                . "رقم المرجع: {$transaction->reference_number}\n"
                . "العميل: {$this->clientName} ({$this->clientMobile})\n"
                . "المبلغ: {$this->amount} EGP\n"
                . "العمولة: {$this->commission} EGP\n"
                . "الخصم: {$discountDisplay} EGP\n"
                . "المرسل: {$this->senderMobile}\n"
                . "الخط: {$this->selectedLineId}\n"
                . "الفرع: {$transaction->branch->name}\n"
                . "الملاحظة: {$this->discountNotes}\n"
                . "معرف المعاملة: {$transaction->id}";
            $admins = \App\Domain\Entities\User::role('admin')->get();
            $supervisors = \App\Domain\Entities\User::role('general_supervisor')->get();
            $recipients = $admins->merge($supervisors)->unique('id');
           
            \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\AdminNotification($adminNotificationMessage, route('transactions.edit', $transaction->reference_number)));
          
        } catch (\Exception $e) {
            // Log the error
          
            
            // Don't throw the exception to avoid breaking the transaction
            // Just log it for debugging
        }
    }

    public function submitTransaction()
    {
        // Custom validation for mobile numbers (only on submit, not while typing)
        $this->validateMobileNumbers();

        $this->validate();

        // Branch validation is handled in CreateTransaction use case based on the selected line's branch

        // --- Customer creation or lookup logic ---

        $customer = Customer::where('mobile_number', $this->clientMobile)->first();
        if (!$customer) {
            // Generate unique customer code
            do {
                $code = 'C' . date('ym') . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            } while (Customer::where('customer_code', $code)->exists());

            $user = Auth::user();
            // Use selected branch if available, otherwise use user's branch
            $branchId = $this->canSelectBranch && $this->selectedBranchId ? $this->selectedBranchId : ($user ? $user->branch_id : null);
            $customer = Customer::create([
                'name' => $this->clientName,
                'mobile_number' => $this->clientMobile,
                'customer_code' => $code,
                'gender' => $this->clientGender ?: 'male',
                'is_client' => false, // Customers created via receive transactions should have inactive wallets
                'agent_id' => $user ? $user->id : null,
                'branch_id' => $branchId,
                'balance' => 0,
            ]);
        }
        // Set Livewire properties for use in transaction
        $this->clientId = $customer->id;
        $this->clientCode = $customer->customer_code;

        // Lookup line and safe
        $line = Line::find($this->selectedLineId);
        $safe = $line->branch->safe;
        if (!$line || !$safe) {
            $this->errorMessage = 'خط أو خزينة غير موجودة.';
            return;
        }

        // Discount must not exceed commission
        $amount = (float) $this->amount;
        $discount = (float) $this->discount;
        $baseCommission = $this->calculateBaseCommission($amount);
        if ($discount > $baseCommission) {
            $this->errorMessage = "الخصم ({$discount} EGP) لا يمكن أن يكون أكبر من العمولة المسموح بها ({$baseCommission} EGP). يرجى إدخال خصم أقل من أو يساوي العمولة.";
            return;
        }

        // Limit checks
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $todayStart = now()->startOfDay();
        $todayEnd = now()->endOfDay();
        $monthlyReceived = \App\Models\Domain\Entities\Transaction::where('line_id', $line->id)
            ->whereIn('transaction_type', ['Receive', 'Deposit'])
            ->whereBetween('created_at', [$monthStart, $monthEnd])
            ->sum('amount');
        $dailyReceived = \App\Models\Domain\Entities\Transaction::where('line_id', $line->id)
            ->whereIn('transaction_type', ['Receive', 'Deposit'])
            ->whereBetween('created_at', [$todayStart, $todayEnd])
            ->sum('amount');
        if ($monthlyReceived + $this->amount > $line->monthly_limit) {
            $exceededBy = ($monthlyReceived + $this->amount) - $line->monthly_limit;
            $this->errorMessage = "هذه المعاملة ستتجاوز الحد الشهري للخط. الاستخدام الشهري الحالي: {$monthlyReceived} EGP, الحد: {$line->monthly_limit} EGP. ستتجاوز ب: {$exceededBy} EGP.";
            return;
        }
        if ($dailyReceived + $this->amount > $line->daily_limit) {
            $exceededBy = ($dailyReceived + $this->amount) - $line->daily_limit;
            $this->errorMessage = "هذه المعاملة ستتجاوز الحد اليومي للخط. الاستخدام اليومي الحالي: {$dailyReceived} EGP, الحد: {$line->daily_limit} EGP. ستتجاوز ب: {$exceededBy} EGP.";
            return;
        }

        // Transaction creation
        DB::beginTransaction();
        try {
            $createTransactionUseCase = app(\App\Application\UseCases\CreateTransaction::class);
            $createdTransaction = $createTransactionUseCase->execute(
                $this->clientName,                // customerName
                $this->clientMobile,              // customerMobileNumber
                $this->clientCode,                // customerCode
                $this->amount,                    // amount
                $this->commission,                // commission
                $this->discount ?? 0,             // deduction (ensure float, never null)
                'Receive',                        // transactionType
                (Auth::user() ? Auth::user()->id : null), // agentId
                $this->selectedLineId,            // lineId
                $safe->id,                        // safeId
                false,                            // isAbsoluteWithdrawal
                'branch safe',                    // paymentMethod
                $this->clientGender ?: 'Male',    // gender
                true,                             // isClient
                $this->senderMobile,              // receiverMobileNumber
                $this->discountNotes,             // discountNotes
                null                              // notes
            );

            // Decrease daily_remaining and monthly_remaining of the line by the received amount
            $line->daily_remaining = max(0, ($line->daily_remaining ?? 0) - $this->amount);
            $line->monthly_remaining = max(0, ($line->monthly_remaining ?? 0) - $this->amount);
            $line->save();

            // Only if transaction was created, proceed with notification and redirect
            if ($createdTransaction) {
                // Log for debugging
               
                
                if (($this->discount ?? 0) > 0) {
                    // Notify admin for approval - using direct notification like Send component
                 
                    $discountDisplay = $this->discount ?? 0;
                    $adminNotificationMessage = "تم إنشاء معاملة إستلام بخصم {$discountDisplay} EGP.\n"
                        . "Transaction Details:" . "\n"
                        . "Reference Number: {$createdTransaction->reference_number}\n"
                        . "Client: {$this->clientName} ({$this->clientMobile})\n"
                        . "Amount: {$this->amount} EGP\n"
                        . "Commission: {$this->commission} EGP\n"
                        . "Discount: {$discountDisplay} EGP\n"
                        . "Sender: {$this->senderMobile}\n"
                        . "Line: {$this->selectedLineId}\n"
                        . "Branch: {$createdTransaction->branch->name}\n"
                        . "Note: {$this->discountNotes}\n"
                        . "Transaction ID: {$createdTransaction->id}";
                    $admins = \App\Domain\Entities\User::role('admin')->get();
                    $supervisors = \App\Domain\Entities\User::role('general_supervisor')->get();
                    $recipients = $admins->merge($supervisors)->unique('id');
                    \Illuminate\Support\Facades\Notification::send($recipients, new \App\Notifications\AdminNotification($adminNotificationMessage, route('transactions.edit', $createdTransaction->reference_number)));
                    
                    DB::commit();
                    // Print receipt and redirect to receipt page for transactions with discount
                    app(\App\Services\ReceiptPrinterService::class)->printReceipt($createdTransaction, 'html');
                    return redirect()->route('transactions.receipt', ['referenceNumber' => $createdTransaction->reference_number]);
                } else {
                    DB::commit();
                    return redirect()->route('transactions.receipt', ['referenceNumber' => $createdTransaction->reference_number]);
                }
            } else {
                DB::rollBack();
                session()->flash('error', 'تعذر إنشاء المعاملة.');
                return;
            }
        } catch (\Exception $e) {
            DB::rollBack();
            $this->errorMessage = 'Exception: ' . $e->getMessage();
            return;
        }
    }

    private function resetForm()
    {
        $this->reset([
            'clientMobile',
            'clientName',
            'clientGender',
            'clientCode',
            'clientId',
            'clientBalance',
            'senderMobile',
            'amount',
            'commission',
            'discount',
            'discountNotes',
            'selectedLineId',
            'clientSuggestions',
            'safeBalanceWarning',
            'errorMessage',
            'mobileFilledFromDropdown'
        ]);
        $this->loadAvailableLines();
    }

    // Public method for Livewire/Blade to call
    public function resetTransactionForm()
    {
        $this->resetForm();
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

        // Validate sender mobile number
        if (empty($this->senderMobile)) {
            $this->addError('senderMobile', 'رقم هاتف المرسل مطلوب.');
            return;
        }
        
        if (!preg_match('/^\d{11}$/', $this->senderMobile)) {
            $this->addError('senderMobile', 'رقم هاتف المرسل يجب أن يكون 11 رقم.');
            return;
        }
    }

    public function render()
    {
        return view('livewire.transactions.receive');
    }
}
