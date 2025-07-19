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
    #[Validate('required|string|max:20')]
    public $clientMobile = '';

    #[Validate('required|string|max:255')]
    public $clientName = '';

    #[Validate('nullable|in:male,female')]
    public $clientGender = '';

    public $clientCode = '';
    public $clientId = null;
    public $clientBalance = null;

    // Sender Information
    #[Validate('required|string|max:20')]
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

    // Form validation messages
    protected $messages = [
        'amount.multiple_of' => 'Amount must be a multiple of 5 EGP.',
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
        // Commission: 5 EGP per 500 EGP increment
        $this->commission = ceil($amount / 500) * 5;
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
                    'display' => $line->mobile_number . ' (' . number_format($line->current_balance, 2) . ' EGP) - ' . ucfirst($line->network),
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
        $adminNotificationMessage = "A receive transaction was created with a discount of {$this->discount} EGP.\n"
            . "Transaction Details:" . "\n"
            . "Reference Number: {$transaction->reference_number}\n"
            . "Client: {$this->clientName} ({$this->clientMobile})\n"
            . "Amount: {$this->amount} EGP\n"
            . "Commission: {$this->commission} EGP\n"
            . "Discount: {$this->discount} EGP\n"
            . "Sender: {$this->senderMobile}\n"
            . "Line: {$this->selectedLineId}\n"
            . "Branch: {$transaction->branch->name}\n"
            . "Note: {$this->discountNotes}\n"
            . "Transaction ID: {$transaction->id}";
        $admins = \App\Domain\Entities\User::role('admin')->get();
        \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification($adminNotificationMessage, route('transactions.edit', $transaction->id)));
    }

    public function submitTransaction()
    {
        $this->validate();

        // Lookup line and safe
        $line = Line::find($this->selectedLineId);
        $safe = $line->branch->safe;
        if (!$line || !$safe) {
            $this->errorMessage = 'Line or Safe not found.';
            return;
        }

        // Discount must not exceed commission
        $amount = (float) $this->amount;
        $discount = (float) $this->discount;
        $baseCommission = ceil($amount / 500) * 5;
        if ($discount > $baseCommission) {
            $this->errorMessage = "Discount ({$discount} EGP) cannot be greater than the allowed commission ({$baseCommission} EGP). Please enter a discount less than or equal to the commission.";
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
            $this->errorMessage = "This transaction would exceed the line's monthly receive limit. Current monthly usage: {$monthlyReceived} EGP, Limit: {$line->monthly_limit} EGP. You would exceed by: {$exceededBy} EGP.";
            return;
        }
        if ($dailyReceived + $this->amount > $line->daily_limit) {
            $exceededBy = ($dailyReceived + $this->amount) - $line->daily_limit;
            $this->errorMessage = "This transaction would exceed the line's daily receive limit. Current daily usage: {$dailyReceived} EGP, Limit: {$line->daily_limit} EGP. You would exceed by: {$exceededBy} EGP.";
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
                $this->discount,                  // deduction
                'Receive',                        // transactionType
                auth()->id(),                     // agentId
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

            // Only if transaction was created, proceed with notification and redirect
            if ($createdTransaction) {
                if ($this->discount > 0) {
                    // Notify admin for approval
                    $this->notifyAdmin($createdTransaction);
                    DB::commit();
                    // Print receipt and redirect to receipt page for transactions with discount
                    app(\App\Services\ReceiptPrinterService::class)->printReceipt($createdTransaction, 'html');
                    return redirect()->route('transactions.receipt', ['transaction' => $createdTransaction->id]);
                } else {
                    DB::commit();
                    return redirect()->route('transactions.receipt', ['transaction' => $createdTransaction->id]);
                }
            } else {
                DB::rollBack();
                session()->flash('error', 'Transaction could not be created.');
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
            'errorMessage'
        ]);
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
            'clientSuggestions'
        ]);
    }

    public function render()
    {
        return view('livewire.transactions.receive');
    }
}
