<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;
use App\Domain\Entities\User;
use App\Application\UseCases\CreateTransaction;
use App\Domain\Interfaces\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Safe;
use App\Helpers\helpers;
use App\Models\CustomExpenseType;

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
    public $showExpenseWithdrawal = false;
    public $destinationBranchId = null;
    public $destinationBranches = [];

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

    public function updatedSelectedBranchId($value)
    {
        // When the source branch changes (for branch or expense withdrawal), update safes dropdown to only show safes from that branch
        if (in_array($this->withdrawalType, ['branch', 'expense'])) {
            $this->safes = \App\Models\Domain\Entities\Safe::where('branch_id', $value)->get()->map(function ($safe) {
                return [
                    'id' => $safe->id,
                    'name' => $safe->name,
                    'current_balance' => $safe->current_balance,
                ];
            })->toArray();
            // Only set safeId if not already set or if the selected safe is not in the new safes list
            $safeIds = array_column($this->safes, 'id');
            if (!$this->safeId || !in_array($this->safeId, $safeIds)) {
                $this->safeId = count($this->safes) > 0 ? $this->safes[0]['id'] : null;
            }
        }
    }

    public function updatedSelectedExpenseItem($value)
    {
        // Clear custom expense item when a different option is selected
        if ($value !== 'other') {
            $this->customExpenseItem = '';
        }
    }

    public function addCustomExpenseType()
    {
        // This method will be called when a custom expense type is submitted
        // Refresh the expense items list to include the new custom type
        $this->expenseItems = CustomExpenseType::getAllExpenseTypes();
    }

    public function mount()
    {
        // $this->authorize('withdraw-cash'); // Removed to allow all users
        parent::mount();
        $this->transactionType = 'Withdrawal';
        $this->withdrawalType = 'direct';
        $this->branchUsers = User::all(); // Show all users, not just branch users
        $user = Auth::user();
        $this->branches = \App\Models\Domain\Entities\Branch::where('id', '!=', $user->branch_id)->get()->map(function ($branch) {
            return [
                'id' => $branch->id,
                'name' => $branch->name,
                'branch_code' => $branch->branch_code,
            ];
        })->toArray();
        if ($user->hasRole('admin') || $user->hasRole('general_supervisor')) {
            $this->destinationBranches = \App\Models\Domain\Entities\Branch::all()->map(function ($branch) {
                return [
                    'id' => $branch->id,
                    'name' => $branch->name,
                ];
            })->toArray();
        }

        // Load expense items (built-in + custom)
        $this->expenseItems = CustomExpenseType::getAllExpenseTypes();

        $user = Auth::user();
        // Fix: Only admins/supervisors see all safes, others see only their branch's safes
        if ($this->withdrawalType === 'branch' && $this->selectedBranchId) {
            $this->safes = \App\Models\Domain\Entities\Safe::where('branch_id', $this->selectedBranchId)->get()->map(function ($safe) {
                return [
                    'id' => $safe->id,
                    'name' => $safe->name,
                    'current_balance' => $safe->current_balance,
                ];
            })->toArray();
        } else if ($user->hasRole('admin') || $user->hasRole('general_supervisor')) {
            $this->safes = \App\Models\Domain\Entities\Safe::all()->map(function ($safe) {
                return [
                    'id' => $safe->id,
                    'name' => $safe->name,
                    'current_balance' => $safe->current_balance,
                ];
            })->toArray();
        } else {
            $this->safes = \App\Models\Domain\Entities\Safe::where('branch_id', $user->branch_id)->get()->map(function ($safe) {
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
        // Only show expense withdrawal type for admin and supervisor
        $this->showExpenseWithdrawal = $user->hasRole('admin') || $user->hasRole('general_supervisor');
    }

    public function updatedClientSearch()
    {
        $search = trim($this->clientSearch);
        if (strlen($search) >= 2) {
            $clients = \App\Models\Domain\Entities\Customer::where(function ($query) use ($search) {
                $query->where('mobile_number', 'like', "%$search%")
                    ->orWhere('customer_code', 'like', "%$search%")
                    ->orWhere('name', 'like', "%$search%");
            })
                ->where('is_client', true)
                ->limit(8)
                ->get(['id', 'name', 'mobile_number', 'customer_code', 'balance', 'is_client']);
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
            $this->clientSearch = $client->name . ' - ' . $client->mobile_number;
        }
    }

    public function clearClientSelection()
    {
        $this->clientId = null;
        $this->clientName = '';
        $this->clientMobile = '';
        $this->clientCode = '';
        $this->clientBalance = 0;
        $this->clientSearch = '';
        $this->clientSuggestions = [];
    }

    public function submitWithdrawal()
    {
        $rules = [
            'amount' => 'required|numeric|min:0.01',
            'safeId' => 'required|exists:safes,id',
            'notes' => 'nullable|string|max:500',
        ];
        if ($this->withdrawalType === 'direct') {
            $rules['customerName'] = 'required|string';
            $rules['nationalId'] = 'required|string|digits:14';
        }
        if ($this->withdrawalType === 'client_wallet') {
            $rules['clientSearch'] = 'required|string|min:3';
            $rules['withdrawalToName'] = 'required|string';
            $rules['withdrawalNationalId'] = 'required|string|digits:14';
        }
        $this->validate($rules);

        // Check if branch is active before proceeding
        try {
            $agent = Auth::user();
            $safe = \App\Models\Domain\Entities\Safe::find($this->safeId);
            if ($safe && $safe->branch_id) {
                \App\Helpers\BranchStatusHelper::validateBranchActive($safe->branch_id);
            }
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return;
        }

        // Check safe balance for all withdrawal types
        $safe = \App\Models\Domain\Entities\Safe::find($this->safeId);
        if (!$safe) {
            session()->flash('error', 'خزينة غير موجودة.');
            return;
        }

        if ($safe->current_balance < $this->amount) {
            session()->flash('error', 'رصيد خزينة الفرع غير كافي. رصيد الخزينة: ' . number_format($safe->current_balance, 2) . ' ج.م');
            return;
        }

        // If this withdrawal is associated with a line, check line limits before saving
        if ($this->lineId) {
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
                    session()->flash('error', 'هذه المعاملة ستتجاوز الحد الشهري للخط. الخط سيتم إنهاؤه حتى بداية الشهر القادم.');
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
                    session()->flash('error', 'هذه المعاملة ستتجاوز الحد اليومي للخط. الخط سيتم إنهاؤه حتى نهاية اليوم.');
                    return;
                }
            }
        }

        // Additional validation for client wallet withdrawals
        if ($this->withdrawalType === 'client_wallet') {
            if (!$this->clientId) {
                session()->flash('error', 'يرجى اختيار عميل أولاً.');
                return;
            }

            $client = \App\Models\Domain\Entities\Customer::find($this->clientId);
            if (!$client) {
                session()->flash('error', 'العميل غير موجود.');
                return;
            }

            if ($client->balance < $this->amount) {
                session()->flash('error', 'رصيد العميل غير كافي. رصيد العميل: ' . number_format($client->balance, 2) . ' ج.م');
                return;
            }
        }

        // Additional validation for branch withdrawals
        if ($this->withdrawalType === 'branch') {
            if (!$this->selectedBranchId) {
                session()->flash('error', 'يرجى اختيار فرع.');
                return;
            }

            $destinationSafe = \App\Models\Domain\Entities\Safe::where('branch_id', $this->selectedBranchId)->first();
            if (!$destinationSafe) {
                session()->flash('error', 'خزينة الفرع غير موجودة.');
                return;
            }

            if ($destinationSafe->current_balance < $this->amount) {
                session()->flash('error', 'رصيد خزينة الفرع غير كافي. رصيد الخزينة: ' . number_format($destinationSafe->current_balance, 2) . ' ج.م');
                return;
            }
        }

        // Additional validation for user withdrawals
        if ($this->withdrawalType === 'user') {
            if (!$this->userId) {
                session()->flash('error', 'يرجى اختيار مستخدم.');
                return;
            }
        }

        // Additional validation for expense withdrawals
        if ($this->withdrawalType === 'expense') {
            if (!$this->selectedBranchId) {
                session()->flash('error', 'يرجى اختيار فرع.');
                return;
            }

            if (!$this->selectedExpenseItem) {
                session()->flash('error', 'يرجى اختيار نوع المصروف.');
                return;
            }

            if ($this->selectedExpenseItem === 'other' && empty(trim($this->customExpenseItem))) {
                session()->flash('error', 'يرجى تحديد نوع المصروف المخصص.');
                return;
            }

            // Check if user has permission to access this branch
            $user = Auth::user();
            if (!$user->hasRole('admin') && !$user->hasRole('general_supervisor') && $user->branch_id != $this->selectedBranchId) {
                session()->flash('error', 'لا يمكنك إنشاء مصروفات لفروع أخرى.');
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

            // Check if user has admin or supervisor privileges - automatic approval for them
            $currentUser = Auth::user();
            $isAdminOrSupervisor = $currentUser && ($currentUser->hasRole('admin') || $currentUser->hasRole('general_supervisor'));

            // Use this status value for all withdrawal types
            $status = $isAdminOrSupervisor ? 'completed' : 'pending';

            if ($this->withdrawalType === 'user') {
                $user = User::find($this->userId);
                if (!$user) {
                    session()->flash('error', 'المستخدم غير موجود.');
                    return;
                }
                $customerName = $user->name;
                $agent = Auth::user();
                $isAdmin = ($agent->role ?? null) === 'admin';
                $status = $isAdminOrSupervisor ? 'completed' : 'pending';
                $safe = \App\Models\Domain\Entities\Safe::find($this->safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);
                $cashTx = \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => $customerName,
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $this->safeId,
                    'transaction_type' => 'Withdrawal',
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                    'reference_number' => $referenceNumber,
                ]);
                // Deduct from safe balance immediately if admin or supervisor
                if ($status === 'completed' && $safe) {
                    $safe->current_balance -= abs($this->amount);
                    $safe->save();
                }
                // Send notification to all admins
                if ($status === 'completed') {
                    $admins = \App\Domain\Entities\User::role('admin')->get();
                    $message = "User withdrawal request: User: $customerName, Amount: " . number_format($this->amount, 2) . " EGP, Safe: " . ($safe->name ?? 'Unknown') . ", By: " . $agent->name . ".";
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification($message, route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id])));
                }
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                
                if ($isAdminOrSupervisor) {
                    // For admin/supervisor, redirect to receipt
                    $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                } else {
                    // For regular users, show waiting approval screen
                    return redirect()->route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id]);
                }
                return;
            }
            if ($this->withdrawalType === 'client_wallet') {
                $paymentMethod = 'client wallet';
                if (!$this->clientId) {
                    session()->flash('error', 'يرجى اختيار عميل صالح.');
                    return;
                }
                $client = \App\Models\Domain\Entities\Customer::find($this->clientId);
                if (!$client || !$client->is_client) {
                    session()->flash('error', 'لا يوجد محفظة لهذا العميل.');
                    return;
                }

                // Check if customer has sufficient balance
                if ($client->balance < $this->amount) {
                    session()->flash('error', 'رصيد العميل غير كافي.');
                    return;
                }

                // Check if the resulting balance would exceed the database constraint limits
                $newBalance = $client->balance - $this->amount;
                if ($newBalance > 1000000) {
                    session()->flash('error', 'لا يمكن إجراء السحب. الرصيد الناتج سيتجاوز الحد الأقصى المسموح به (1,000,000 ج.م).');
                    return;
                }
                if ($newBalance < -1000000) {
                    session()->flash('error', 'لا يمكن إجراء السحب. الرصيد الناتج سيتجاوز الحد الأدنى المسموح به (-1,000,000 ج.م).');
                    return;
                }

                $customerName = $client->name;
                $customerMobileNumber = $client->mobile_number;
                $customerCode = $client->customer_code;
                $isClient = true;
                $notes = $this->notes . ' | Withdrawal to: ' . $this->withdrawalToName;
                $user = $agent;
                $isAdmin = ($user->role ?? null) === 'admin';
                $status = $isAdminOrSupervisor ? 'completed' : 'pending';
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);

                // Only deduct balances if admin or supervisor
                if ($isAdminOrSupervisor) {
                    $client->balance -= $this->amount;
                    $client->save();
                    if ($safe) {
                        $safe->current_balance -= $this->amount;
                        $safe->save();
                    }
                }

                $cashTx = \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => $customerName,
                    'customer_code' => $customerCode,
                    'amount' => abs($this->amount),
                    'notes' => $notes . ' | CLIENT_WALLET_WITHDRAWAL',
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'depositor_national_id' => $this->withdrawalNationalId,
                    'depositor_mobile_number' => $this->clientMobile,
                    'agent_id' => $user->id,
                    'reference_number' => $referenceNumber,
                ]);
                // Send notification to all admins
                if ($status === 'completed') {
                    $admins = \App\Domain\Entities\User::role('admin')->get();
                    $message = "Client wallet withdrawal request: Client: $customerName, Amount: " . number_format($this->amount, 2) . " EGP, Safe: " . ($safe->name ?? 'Unknown') . ", By: " . $user->name . ".";
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification($message, route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id])));
                }
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                
                if ($isAdminOrSupervisor) {
                    // For admin/supervisor, redirect to receipt
                    $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                } else {
                    // For regular users, show waiting approval screen
                    return redirect()->route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id]);
                }
                return;
            }
            if ($this->withdrawalType === 'direct') {
                $user = Auth::user();
                $isAdmin = ($user->role ?? null) === 'admin';
                $status = $isAdminOrSupervisor ? 'completed' : 'pending';
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);
                $cashTx = \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => $customerName,
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                    'reference_number' => $referenceNumber,
                ]);
                // Deduct from safe balance immediately if completed
                if ($status === 'completed' && $safe) {
                    $safe->current_balance -= abs($this->amount);
                    $safe->save();
                }
                // Send notification to all admins
                if ($status === 'completed') {
                    $admins = \App\Domain\Entities\User::role('admin')->get();
                    $message = "Direct withdrawal request: Amount: " . number_format($this->amount, 2) . " EGP, Safe: " . ($safe->name ?? 'Unknown') . ", By: " . $user->name . ".";
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification($message, route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id])));
                }
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                if ($isAdminOrSupervisor) {
                    // For admin/supervisor, redirect to receipt
                    $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                } else {
                    // For regular users, show waiting approval screen
                    return redirect()->route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id]);
                }
                return;
            }

            if ($this->withdrawalType === 'admin') {
                $user = Auth::user();
                $isAdmin = ($user->role ?? null) === 'admin';
                $status = $isAdminOrSupervisor ? 'completed' : 'pending';
                $safe = \App\Models\Domain\Entities\Safe::find($this->safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);
                $cashTx = \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => 'Admin',
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $this->safeId,
                    'transaction_type' => 'Withdrawal',
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                    'reference_number' => $referenceNumber,
                ]);
                // Deduct from safe balance immediately if admin or supervisor
                if ($status === 'completed' && $safe) {
                    $safe->current_balance -= abs($this->amount);
                    $safe->save();
                }
                // Send notification to all admins
                if ($status === 'completed') {
                    $admins = \App\Domain\Entities\User::role('admin')->get();
                    $message = "Admin withdrawal request: Amount: " . number_format($this->amount, 2) . " EGP, Safe: " . ($safe->name ?? 'Unknown') . ", By: " . $user->name . ".";
                    \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification($message, route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id])));
                }
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId']);
                if ($isAdminOrSupervisor) {
                    // For admin/supervisor, redirect to receipt
                    $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                } else {
                    // For regular users, show waiting approval screen
                    return redirect()->route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id]);
                }
                return;
            }

            if ($this->withdrawalType === 'branch') {
                $user = Auth::user();
                // Always use the safe from the selected source branch for deduction
                $sourceSafe = \App\Models\Domain\Entities\Safe::where('branch_id', $this->selectedBranchId)->first();
                if ($user->hasRole('admin') || $user->hasRole('general_supervisor')) {
                    $destinationBranch = \App\Models\Domain\Entities\Branch::find($this->destinationBranchId);
                    $destinationSafe = $destinationBranch ? $destinationBranch->safe : null;
                } else {
                    $destinationBranch = $user->branch;
                    $destinationSafe = $destinationBranch ? $destinationBranch->safe : null;
                }
                $destinationSafeId = $destinationSafe ? $destinationSafe->id : null;
                $destinationBranchName = $destinationBranch ? $destinationBranch->name : 'Unknown Branch';
                $referenceNumber = generate_reference_number($destinationBranchName);
                $cashTx = \App\Models\Domain\Entities\CashTransaction::create([
                    'customer_name' => 'Branch Transfer: ' . $destinationBranchName,
                    'amount' => abs($this->amount),
                    'notes' => $this->notes,
                    'safe_id' => $sourceSafe ? $sourceSafe->id : null, // Use correct source safe
                    'transaction_type' => $transactionType,
                    'status' => $status, // Use the determined status
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                    'destination_branch_id' => $destinationBranch ? $destinationBranch->id : null,
                    'destination_safe_id' => $destinationSafeId,
                    'reference_number' => $referenceNumber,
                ]);

                // If auto-approved, update balances immediately
                if ($status === 'completed' && $destinationSafe && $sourceSafe) {
                    if ($sourceSafe->current_balance >= $this->amount) {
                        $sourceSafe->current_balance -= $this->amount;
                        $sourceSafe->save();
                        $destinationSafe->current_balance += $this->amount;
                        $destinationSafe->save();
                    }
                }

                // Define $admins before sending notifications
                $admins = \App\Domain\Entities\User::role('admin')->get();
                $sourceBranchName = $sourceSafe && $sourceSafe->branch ? $sourceSafe->branch->name : 'Unknown';
                $message = "Branch withdrawal request: From Branch: " . $sourceBranchName . " To Branch: " . $destinationBranchName . ", Amount: " . number_format($this->amount, 2) . " EGP, By: " . $user->name . ".";
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\AdminNotification($message, url()->current()));
                
                if ($isAdminOrSupervisor) {
                    // For admin/supervisor, redirect to receipt
                    session()->flash('message', 'Branch withdrawal created successfully!');
                    $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId', 'destinationBranchId']);
                    $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                } else {
                    // For regular users, show waiting approval screen
                    session()->flash('message', 'Branch withdrawal submitted for admin approval!');
                    $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId', 'destinationBranchId']);
                    return redirect()->route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id]);
                }
                return; // Prevent double deduction/addition
            }

            if ($this->withdrawalType === 'expense') {
                $user = Auth::user();
                $branch = collect($this->branches)->firstWhere('id', $this->selectedBranchId);
                $branchName = $branch['name'] ?? 'Unknown Branch';

                // Get expense item name and save custom types
                $expenseItemName = '';
                if ($this->selectedExpenseItem === 'other') {
                    $expenseItemName = $this->customExpenseItem;
                    // Save the custom expense type for future use
                    CustomExpenseType::addOrUpdateType($this->customExpenseItem, $this->customExpenseItem);
                    // Refresh the expense items list
                    $this->addCustomExpenseType();
                } elseif (str_starts_with($this->selectedExpenseItem, 'custom_')) {
                    // Handle custom expense type selection
                    $customId = str_replace('custom_', '', $this->selectedExpenseItem);
                    $customType = CustomExpenseType::getCustomTypeById($customId);
                    if ($customType) {
                        $expenseItemName = $customType->name_ar;
                        // Increment usage count
                        $customType->increment('usage_count');
                    } else {
                        $expenseItemName = 'Unknown Custom Expense';
                    }
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
                    'status' => $status, // Use the determined status
                    'transaction_date_time' => now(),
                    'agent_id' => $user->id,
                    'destination_branch_id' => $this->selectedBranchId,
                    'destination_safe_id' => $this->safeId, // Same safe for expense
                    'reference_number' => generate_reference_number($branchName),
                ]);
                // Deduct from safe balance immediately if admin or supervisor
                if ($status === 'completed' && $safe) {
                    $safe->current_balance -= abs($this->amount);
                    $safe->save();
                }

                // Send notification to admins
                $admins = \App\Domain\Entities\User::role(['admin', 'general_supervisor'])->get();
                $notificationMessage = "Expense withdrawal request: " . number_format($this->amount, 2) . " EGP for " . $expenseItemName . " in " . $branchName . " branch by " . $user->name . " requires approval.";

                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\AdminNotification(
                        $notificationMessage,
                        route('transactions.pending')
                    ));
                }

                if ($isAdminOrSupervisor) {
                    // For admin/supervisor, redirect to receipt
                    session()->flash('message', 'Expense withdrawal created successfully!');
                    $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId', 'selectedExpenseItem', 'customExpenseItem']);
                    $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                } else {
                    // For regular users, show waiting approval screen
                    session()->flash('message', 'Expense withdrawal request submitted for admin approval!');
                    $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber', 'clientSearch', 'clientSuggestions', 'clientName', 'clientMobile', 'clientBalance', 'clientId', 'withdrawalNationalId', 'withdrawalToName', 'selectedBranchId', 'selectedExpenseItem', 'customExpenseItem']);
                    return redirect()->route('transactions.cash.waiting-approval', ['cashTransaction' => $cashTx->id]);
                }
                return;
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
            'notes' => 'required|string',
            'safeId' => 'required|integer|exists:safes,id',
        ];
        if ($this->withdrawalType === 'direct') {
            $rules['customerName'] = 'required|string';
            $rules['nationalId'] = 'required|string|digits:14';
            $rules['clientMobile'] = 'required|digits:11';
        }
        if ($this->withdrawalType === 'client_wallet') {
            $rules['clientSearch'] = 'required|string|min:3';
            $rules['withdrawalToName'] = 'required|string';
            $rules['withdrawalNationalId'] = 'required|string|digits:14';
            $rules['clientMobile'] = 'required|digits:11';
        }
        if ($this->withdrawalType === 'expense') {
            $rules['selectedExpenseItem'] = 'required|string';
            if ($this->selectedExpenseItem === 'other') {
                $rules['customExpenseItem'] = 'required|string|min:2';
            }
        }
        return $rules;
    }

    public function render()
    {
        Log::info('WITHDRAWAL COMPONENT RENDERED');
        return view('livewire.transactions.withdrawal', [
            'users' => $this->branchUsers,
        ]);
    }
}
