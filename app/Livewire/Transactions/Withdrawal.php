<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;
use App\Domain\Entities\User;
use App\Application\UseCases\CreateTransaction;
use App\Domain\Interfaces\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Safe;

class Withdrawal extends Create
{
    public $withdrawalType = 'direct';
    public $customerName, $amount, $notes;
    public $userId, $branchUsers = [];
    public $clientCode, $clientNumber, $clientNationalNumber;
    public $safeId = 0;
    public $nationalId;
    public $withdrawalToName;
    public $safes = [];
    public $clientSearch = '';
    public $clientSuggestions = [];
    public $clientName = '';
    public $clientMobile = '';
    public $clientBalance = 0;
    public $clientId = null;
    public $withdrawalNationalId;
    public $branches = [];
    public $selectedBranchId = null;
    public $expenseItems = [];
    public $selectedExpenseItem = null;
    public $customExpenseItem = '';

    protected $casts = [
        'safeId' => 'integer',
    ];

    private CreateTransaction $createTransactionUseCase;
    private CustomerRepository $customerRepository;

    public function boot(CreateTransaction $createTransactionUseCase, CustomerRepository $customerRepository)
    {
        $this->createTransactionUseCase = $createTransactionUseCase;
        $this->customerRepository = $customerRepository;
    }

    public function mount()
    {
        parent::mount();
        $this->transactionType = 'Withdrawal';
        $this->withdrawalType = 'direct';
        $this->branchUsers = User::where('branch_id', Auth::user()->branch_id ?? null)->get();
        $this->branches = \App\Models\Domain\Entities\Branch::all()->map(function($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'branch_code' => $branch->branch_code,
            ];
        })->toArray();

        // Load expense items
        $this->expenseItems = [
            ['id' => 'electricity', 'name' => 'كهرباء'],
            ['id' => 'water', 'name' => 'مياه'],
            ['id' => 'internet', 'name' => 'إنترنت'],
            ['id' => 'phone', 'name' => 'هاتف'],
            ['id' => 'maintenance', 'name' => 'صيانة'],
            ['id' => 'cleaning', 'name' => 'تنظيف'],
            ['id' => 'security', 'name' => 'أمن'],
            ['id' => 'other', 'name' => 'أخرى'],
        ];

        $user = Auth::user();
        if ($user->hasRole('admin') || $user->hasRole('supervisor')) {
            $this->safes = \App\Models\Domain\Entities\Safe::all()->map(function($safe) {
                return [
                    'id' => $safe->id,
                    'name' => $safe->name,
                    'current_balance' => $safe->current_balance,
                ];
            })->toArray();
        } else {
            $this->safes = \App\Models\Domain\Entities\Safe::where('branch_id', $user->branch_id ?? null)->get()->map(function($safe) {
                return [
                    'id' => $safe->id,
                    'name' => $safe->name,
                    'current_balance' => $safe->current_balance,
                ];
            })->toArray();
        }
        if (count($this->safes) > 0) {
            $this->safeId = $this->safes[0]['id'];
        }
    }

    public function updatedClientSearch()
    {
        $search = $this->clientSearch;
        if (strlen($search) >= 3) {
            $clients = \App\Models\Domain\Entities\Customer::where('mobile_number', 'like', "%$search%")
                ->orWhere('customer_code', 'like', "%$search%")
                ->limit(5)
                ->get(['id', 'name', 'mobile_number', 'customer_code', 'balance']);
            $this->clientSuggestions = $clients->toArray();
        } else {
            $this->clientSuggestions = [];
        }
    }

    public function selectClient($clientId)
    {
        $client = \App\Models\Domain\Entities\Customer::find($clientId);
        if ($client) {
            $this->clientId = $client->id;
            $this->clientName = $client->name;
            $this->clientMobile = $client->mobile_number;
            $this->clientCode = $client->customer_code;
            $this->clientBalance = $client->balance;
            $this->clientSuggestions = [];
            $this->clientSearch = $client->mobile_number;
        }
    }

    public function submitWithdrawal()
    {
        $this->validate([
            'amount' => 'required|numeric|min:0.01',
            'safeId' => 'required|exists:safes,id',
            'notes' => 'nullable|string|max:500',
        ]);

        // Check safe balance for all withdrawal types
        $safe = \App\Models\Domain\Entities\Safe::find($this->safeId);
        if (!$safe) {
            session()->flash('error', 'Safe not found.');
            return;
        }

        if ($safe->current_balance < $this->amount) {
            session()->flash('error', 'Insufficient balance in the selected safe. Available balance: ' . number_format($safe->current_balance, 2) . ' ج.م');
            return;
        }

        // Additional validation for client wallet withdrawals
        if ($this->withdrawalType === 'client_wallet') {
            if (!$this->clientId) {
                session()->flash('error', 'Please select a client first.');
                return;
            }

            $client = \App\Models\Domain\Entities\Customer::find($this->clientId);
            if (!$client) {
                session()->flash('error', 'Selected client not found.');
                return;
            }

            if ($client->balance < $this->amount) {
                session()->flash('error', 'Insufficient client balance. Client balance: ' . number_format($client->balance, 2) . ' ج.م');
                return;
            }
        }

        // Additional validation for branch withdrawals
        if ($this->withdrawalType === 'branch') {
            if (!$this->selectedBranchId) {
                session()->flash('error', 'Please select a branch.');
                return;
            }

            $destinationSafe = \App\Models\Domain\Entities\Safe::where('branch_id', $this->selectedBranchId)->first();
            if (!$destinationSafe) {
                session()->flash('error', 'Selected branch safe not found.');
                return;
            }

            if ($destinationSafe->current_balance < $this->amount) {
                session()->flash('error', 'Insufficient balance in the selected branch safe. Available balance: ' . number_format($destinationSafe->current_balance, 2) . ' ج.م');
                return;
            }
        }

        // Additional validation for user withdrawals
        if ($this->withdrawalType === 'user') {
            if (!$this->userId) {
                session()->flash('error', 'Please select a user.');
                return;
            }
        }

        // Additional validation for expense withdrawals
        if ($this->withdrawalType === 'expense') {
            if (!$this->selectedBranchId) {
                session()->flash('error', 'Please select a branch.');
                return;
            }

            if (!$this->selectedExpenseItem) {
                session()->flash('error', 'Please select an expense type.');
                return;
            }

            if ($this->selectedExpenseItem === 'other' && empty($this->customExpenseItem)) {
                session()->flash('error', 'Please specify the custom expense type.');
                return;
            }

            // Check if user has permission to access this branch
            $user = Auth::user();
            if (!$user->hasRole(['admin', 'general_supervisor']) && $user->branch_id != $this->selectedBranchId) {
                session()->flash('error', 'You can only create expense withdrawals for your own branch.');
                return;
            }
        }

        // Proceed with withdrawal if all validations pass
        $safeId = $this->safeId;
        $customerName = $this->customerName ?? '';
        $customerMobileNumber = $this->customerMobileNumber ?? '';
        $customerCode = $this->customerCode ?? '';
        $commission = 0;
        $deduction = 0;
        $transactionType = 'Withdrawal';
        $branchId = Auth::user()->branch_id;
        $lineId = null;
        $paymentMethod = 'cash';
        $gender = 'male';
        $isClient = false;
        $notes = $this->notes ?? '';

        try {
            $agent = Auth::user();
            $branchId = $agent->branch_id;
            $safeId = $this->safeId ?? 0;
            $lineId = 0;
            $paymentMethod = 'branch safe';
            $customerName = $this->customerName;
            $customerMobileNumber = '';
            $customerCode = '';
            $commission = 0;
            $deduction = 0;
            $gender = 'Male';
            $isClient = false;
            $transactionType = 'Withdrawal';
            $notes = $this->notes;

            if ($this->withdrawalType === 'user') {
                $user = User::find($this->userId);
                $customerName = $user?->name ?? '';
                $agent = Auth::user();
                $status = $agent->hasRole('admin') ? 'completed' : 'pending';
                \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => $customerName,
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $this->safeId,
                    'transaction_type' => 'Withdrawal',
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                ]);
                session()->flash('message', $status === 'completed' ? 'User withdrawal performed successfully!' : 'User withdrawal submitted for admin approval!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                return redirect()->route('transactions.cash.withdrawal');
            }
            if ($this->withdrawalType === 'client_wallet') {
                $paymentMethod = 'client wallet';
                if (!$this->clientId) {
                    session()->flash('error', 'Please select a valid client.');
                    return;
                }
                $client = \App\Models\Domain\Entities\Customer::find($this->clientId);
                if (!$client || !isset($client->balance)) {
                    session()->flash('error', 'No wallet found for this client.');
                    return;
                }
                if ($client->balance < $this->amount) {
                    session()->flash('error', 'Insufficient balance in client wallet.');
                    return;
                }
                $customerName = $client->name;
                $customerMobileNumber = $client->mobile_number;
                $customerCode = $client->customer_code;
                $isClient = true;
                $notes = $this->notes . ' | Withdrawal to: ' . $this->withdrawalToName;
                $user = $agent;
                if ($user->hasRole('admin')) {
                    // Admin: complete immediately and update balances
                    \App\Models\Domain\Entities\CashTransaction::create([
                        'customer_name' => $customerName,
                        'customer_code' => $customerCode,
                        'amount' => abs($this->amount),
                        'notes' => $notes,
                        'safe_id' => $safeId,
                        'transaction_type' => $transactionType,
                        'status' => 'completed',
                        'transaction_date_time' => now(),
                        'depositor_national_id' => $this->withdrawalNationalId,
                        'agent_id' => $user->id,
                    ]);
                    // Deduct from client wallet
                    $client->balance -= abs($this->amount);
                    $client->save();
                    // Deduct from safe
                    $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                    if ($safe) {
                        $safe->current_balance -= abs($this->amount);
                        $safe->save();
                    }
                    session()->flash('message', 'Client wallet withdrawal performed successfully!');
                    $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                    return redirect()->route('transactions.cash.withdrawal');
                } else {
                    // Non-admin: pending, do not update balances
                    \App\Models\Domain\Entities\CashTransaction::create([
                        'customer_name' => $customerName,
                        'customer_code' => $customerCode,
                        'amount' => abs($this->amount),
                        'notes' => $notes,
                        'safe_id' => $safeId,
                        'transaction_type' => $transactionType,
                        'status' => 'pending',
                        'transaction_date_time' => now(),
                        'depositor_national_id' => $this->withdrawalNationalId,
                        'agent_id' => $user->id,
                    ]);
                    session()->flash('message', 'Client wallet withdrawal submitted for admin approval!');
                    $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                    return redirect()->route('transactions.cash.withdrawal');
                }
            }
            if ($this->withdrawalType === 'direct') {
                // Check if safe has sufficient balance
                $safe = Safe::find($safeId);
                if (!$safe || $safe->current_balance < $this->amount) {
                    session()->flash('error', 'Insufficient balance in the selected safe.');
                    return;
                }
                $user = Auth::user();
                $status = $user->hasRole('admin') ? 'completed' : 'pending';
                \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => $customerName,
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                ]);
                if ($user->hasRole('admin')) {
                    $safe->current_balance -= abs($this->amount);
                    $safe->save();
                }
                session()->flash('message', $status === 'completed' ? 'Withdrawal performed successfully!' : 'Withdrawal submitted for admin approval!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                return redirect()->route('transactions.cash.withdrawal');
            }

            if ($this->withdrawalType === 'admin') {
                $user = Auth::user();
                $status = $user->hasRole('admin') ? 'completed' : 'pending';
                \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => 'Admin',
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $this->safeId,
                    'transaction_type' => 'Withdrawal',
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                ]);
                if ($user->hasRole('admin')) {
                    $safe = \App\Models\Domain\Entities\Safe::find($this->safeId);
                    if ($safe) {
                        $safe->current_balance -= abs($this->amount);
                        $safe->save();
                    }
                }
                session()->flash('message', $status === 'completed' ? 'Admin withdrawal performed successfully!' : 'Admin withdrawal submitted for approval!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                return redirect()->route('transactions.cash.withdrawal');
            }

            if ($this->withdrawalType === 'branch') {
                $user = Auth::user();
                $branch = collect($this->branches)->firstWhere('id', $this->selectedBranchId);
                $branchName = $branch['name'] ?? 'Unknown Branch';
                $destinationSafe = \App\Models\Domain\Entities\Safe::where('branch_id', $this->selectedBranchId)->first();
                $cashTx = \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => 'Branch Transfer to: ' . $branchName,
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $this->safeId, // agent's branch safe (destination)
                    'transaction_type' => 'Withdrawal',
                    'status' => 'pending',
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                    'destination_branch_id' => $this->selectedBranchId,
                    'destination_safe_id' => $destinationSafe ? $destinationSafe->id : null,
                ]);
                // Send notification to all admins
                $admins = \App\Domain\Entities\User::role('admin')->get();
                $message = "Branch withdrawal request: " .
                    "From branch: $branchName (ID: {$this->selectedBranchId}), " .
                    "To branch: " . ($user->branch->name ?? 'Unknown') . ", " .
                    "Amount: " . number_format($this->amount, 2) . " EGP, " .
                    "By: " . $user->name . ".";
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification($message, url()->current()));
                session()->flash('message', 'Branch withdrawal submitted for admin approval!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                return redirect()->route('transactions.cash.withdrawal');
            }

            if ($this->withdrawalType === 'expense') {
                $user = Auth::user();
                $branch = collect($this->branches)->firstWhere('id', $this->selectedBranchId);
                $branchName = $branch['name'] ?? 'Unknown Branch';
                
                // Get expense item name
                $expenseItemName = '';
                if ($this->selectedExpenseItem === 'other') {
                    $expenseItemName = $this->customExpenseItem;
                } else {
                    $expenseItem = collect($this->expenseItems)->firstWhere('id', $this->selectedExpenseItem);
                    $expenseItemName = $expenseItem['name'] ?? 'Unknown Expense';
                }

                $cashTx = \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => 'Expense: ' . $expenseItemName . ' - ' . $branchName,
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $this->safeId,
                    'transaction_type' => 'Withdrawal',
                    'status' => 'pending',
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                    'destination_branch_id' => $this->selectedBranchId,
                    'destination_safe_id' => $this->safeId, // Same safe for expense
                ]);

                // Send notification to admins
                $admins = \App\Domain\Entities\User::role(['admin', 'general_supervisor'])->get();
                $notificationMessage = "Expense withdrawal request: " . number_format($this->amount, 2) . " EGP for " . $expenseItemName . " in " . $branchName . " branch by " . $user->name . " requires approval.";
                
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\AdminNotification(
                        $notificationMessage,
                        route('transactions.pending')
                    ));
                }

                session()->flash('message', 'Expense withdrawal request submitted for admin approval!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId', 'selectedExpenseItem', 'customExpenseItem']);
                return redirect()->route('transactions.cash.withdrawal');
            }

            $this->createTransactionUseCase->execute(
                $customerName,
                $customerMobileNumber,
                $customerCode,
                -1 * abs($this->amount), // Negative amount for withdrawal
                $commission,
                $deduction,
                $transactionType,
                $branchId,
                $lineId,
                $safeId,
                $paymentMethod,
                $gender,
                $isClient,
                $notes
            );

            session()->flash('message', 'Withdrawal processed successfully!');
            $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error processing withdrawal: ' . $e->getMessage());
        }
    }

    public function rules()
    {
        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string',
            'safeId' => 'required|integer|exists:safes,id',
        ];
        if ($this->withdrawalType === 'direct') {
            $rules['customerName'] = 'required|string';
            $rules['nationalId'] = 'required|string|min:6';
        }
        if ($this->withdrawalType === 'user') {
            $rules['userId'] = 'required|integer|exists:users,id';
        }
        if ($this->withdrawalType === 'client_wallet') {
            $rules['clientSearch'] = 'required|string|min:3';
            $rules['withdrawalToName'] = 'required|string';
            $rules['withdrawalNationalId'] = 'required|string|min:6';
        }
        return $rules;
    }

    public function render()
    {
        \Log::info('WITHDRAWAL COMPONENT RENDERED');
        return view('livewire.transactions.withdrawal', [
            'users' => $this->branchUsers,
        ]);
    }
}
