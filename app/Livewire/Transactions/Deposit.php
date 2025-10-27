<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;
use App\Domain\Entities\User;
use App\Application\UseCases\CreateTransaction;
use App\Domain\Interfaces\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;
use App\Helpers\helpers;
use Illuminate\Support\Facades\Notification;
use App\Notifications\AdminNotification;
use Illuminate\Validation\Rules\Validate;

class Deposit extends Create
{
    public $depositType = 'direct';
    public $customerName, $amount, $notes;
    public $userId, $branchUsers = [];
    public $clientCode, $clientNumber, $clientNationalNumber;
    public $safeId = 0;
    public $branchSafes = [];
    public $clientSearch = '';
    public $clientSuggestions = [];
    public $clientName = '';
    public $clientMobile = '';
    public $clientBalance = 0;
    public $clientId = null;
    public $depositorNationalId = '';
    public $branches = [];

    #[Validate('nullable|digits:11')]
    public $depositorMobileNumber = '';

    private CreateTransaction $createTransactionUseCase;
    private CustomerRepository $customerRepository;

    public function boot(CreateTransaction $createTransactionUseCase, CustomerRepository $customerRepository)
    {
        $this->createTransactionUseCase = $createTransactionUseCase;
        $this->customerRepository = $customerRepository;
    }

    public function mount()
    {
        // Do NOT call parent::mount() to avoid Gate::authorize restriction
        $this->transactionType = 'Deposit';
        $this->depositType = 'direct';

        $user = Auth::user();
        // Load users for deposit - all users should be available for user deposits
        // For user deposits, show all users regardless of role or branch
        $this->branchUsers = User::with(['roles', 'branch'])->get();

        if ($user->hasRole('admin') || $user->hasRole('general_supervisor')) {
            $this->branchSafes = \App\Models\Domain\Entities\Safe::with('branch')->get();
            $this->branches = \App\Models\Domain\Entities\Branch::all();
        } else {
            $this->branchSafes = \App\Models\Domain\Entities\Safe::where('branch_id', $user->branch_id ?? null)->get();
            $this->branches = \App\Models\Domain\Entities\Branch::where('id', $user->branch_id ?? null)->get();
        }

        if (count($this->branchSafes) > 0) {
            $this->safeId = $this->branchSafes[0]->id;
        }
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
            $this->clientSearch = $client->name . ' (' . $client->mobile_number . ')';
        }
    }

    public function clearClientSelection()
    {
        $this->reset(['clientId', 'clientName', 'clientMobile', 'clientCode', 'clientBalance', 'clientSearch', 'clientSuggestions']);
    }

    public function submitDeposit()
    {
        try {
            $agent = Auth::user();
            
            // Check if branch is active before proceeding
            $safeId = $this->safeId ?? 0;
            $safe = \App\Models\Domain\Entities\Safe::find($safeId);
            if ($safe && $safe->branch_id) {
                \App\Helpers\BranchStatusHelper::validateBranchActive($safe->branch_id);
            }
            $transactionType = 'Deposit';
            $notes = $this->notes;

            // If this deposit is associated with a line, check line limits before saving
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

            // Deposits don't need approval - always completed
            $status = 'completed';

            if ($this->depositType === 'user') {
                $user = User::find($this->userId);
                if (!$user) {
                    session()->flash('message', 'User not found.');
                    return;
                }
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);
                $cashTx = CashTransaction::create([
                    'customer_name' => $user->name,
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                    'reference_number' => $referenceNumber,
                ]);
                // Apply balance changes immediately for deposits
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                // Send notification to admins and supervisors
                $this->sendDepositNotification($cashTx, $agent);
                $this->reset(['userId', 'amount', 'notes', 'customerName', 'clientCode', 'clientNumber', 'clientNationalNumber']);
                $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                return;
            }
            if ($this->depositType === 'client_wallet') {
                if (!$this->clientId) {
                    session()->flash('message', 'Please select a valid client.');
                    return;
                }
                $client = \App\Models\Domain\Entities\Customer::find($this->clientId);
                if (!$client || !$client->is_client) {
                    session()->flash('error', 'لا يوجد محفظة لهذا العميل.');
                    return;
                }

                // Check if the resulting balance would exceed the database constraint limits
                $newBalance = $client->balance + $this->amount;
                if ($newBalance > 1000000) {
                    session()->flash('error', 'لا يمكن إجراء الإيداع. الرصيد الناتج سيتجاوز الحد الأقصى المسموح به (1,000,000 ج.م).');
                    return;
                }
                if ($newBalance < -1000000) {
                    session()->flash('error', 'لا يمكن إجراء الإيداع. الرصيد الناتج سيتجاوز الحد الأدنى المسموح به (-1,000,000 ج.م).');
                    return;
                }

                $client->balance += $this->amount;
                $client->save();
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);
                $cashTx = CashTransaction::create([
                    'customer_name' => $this->clientName,
                    'customer_code' => $this->clientCode,
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'depositor_national_id' => $this->depositorNationalId,
                    'depositor_mobile_number' => $this->depositorMobileNumber,
                    'agent_id' => $agent->id,
                    'reference_number' => $referenceNumber,
                ]);
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                // Send notification to admins and supervisors
                $this->sendDepositNotification($cashTx, $agent);
                $this->reset(['clientId', 'clientName', 'clientMobile', 'clientCode', 'clientBalance', 'clientSearch', 'amount', 'notes', 'userId', 'customerName', 'clientNumber', 'clientNationalNumber', 'depositorNationalId', 'depositorMobileNumber']);
                $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                return;
            }
            if ($this->depositType === 'admin') {
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);
                $cashTx = CashTransaction::create([
                    'customer_name' => 'اداري',
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                    'reference_number' => $referenceNumber,
                ]);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                // Send notification to admins and supervisors
                $this->sendDepositNotification($cashTx, $agent);
                $this->reset(['amount', 'notes', 'userId', 'customerName', 'clientCode', 'clientNumber', 'clientNationalNumber']);
                $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                return;
            }
            if ($this->depositType === 'direct') {
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                $branchName = $safe && $safe->branch ? $safe->branch->name : 'Unknown';
                $referenceNumber = generate_reference_number($branchName);
                $cashTx = CashTransaction::create([
                    'customer_name' => $this->customerName,
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => $status,
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                    'reference_number' => $referenceNumber,
                ]);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                // Send notification to admins and supervisors
                $this->sendDepositNotification($cashTx, $agent);
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber']);
                $this->js('window.location.href = "' . route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]) . '"');
                return;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Deposit failed: ' . $e->getMessage());
        }
    }

    public function rules()
    {
        return [
            'depositorMobileNumber' => 'nullable|digits:11',
            // Add other rules as needed
        ];
    }

    /**
     * Send deposit notification to admins and supervisors
     */
    protected function sendDepositNotification($cashTx, $agent)
    {
        $admins = \App\Domain\Entities\User::role('admin')->get();
        $supervisors = \App\Domain\Entities\User::role('general_supervisor')->get();
        $recipients = $admins->merge($supervisors)->unique('id');
        $safe = $cashTx->safe;
        $branch = $safe ? $safe->branch : null;
        $url = route('cash-transactions.receipt', ['referenceNumber' => $cashTx->reference_number]);
        $customerCode = $cashTx->customer_code ?: 'N/A';
        $message = "معاملة إيداع جديدة\n" .
            "رقم المرجع: {$cashTx->reference_number}\n" .
            "العميل: {$cashTx->customer_name} (" . $customerCode . ")\n" .
            "المبلغ: {$cashTx->amount} EGP\n" .
            "الخزنة: " . ($safe ? $safe->name : 'N/A') . "\n" .
            "الفرع: " . ($branch ? $branch->name : 'N/A') . "\n" .
            "الوكيل: {$agent->name}\n" .
            "الملاحظات: {$cashTx->notes}";
        Notification::send($recipients, new AdminNotification($message, $url));
    }

    public function render()
    {
        return view('livewire.transactions.deposit');
    }
}
