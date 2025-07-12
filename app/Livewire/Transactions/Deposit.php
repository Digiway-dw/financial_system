<?php

namespace App\Livewire\Transactions;

use App\Livewire\Transactions\Create;
use App\Domain\Entities\User;
use App\Application\UseCases\CreateTransaction;
use App\Domain\Interfaces\CustomerRepository;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;

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
        $this->branchUsers = User::where('branch_id', Auth::user()->branch_id ?? null)->get();
        $this->branchSafes = \App\Models\Domain\Entities\Safe::where('branch_id', Auth::user()->branch_id ?? null)->get();
        if (count($this->branchSafes) > 0) {
            $this->safeId = $this->branchSafes[0]->id;
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

    public function submitDeposit()
    {
        try {
            $agent = Auth::user();
            $safeId = $this->safeId ?? 0;
            $transactionType = 'Deposit';
            $notes = $this->notes;

            if ($this->depositType === 'user') {
                $user = User::find($this->userId);
                if (!$user) {
                    session()->flash('message', 'User not found.');
                    return;
                }
                CashTransaction::create([
                    'customer_name' => $user->name,
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => 'Completed',
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                ]);
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                session()->flash('message', 'تم حفظ إيداع المستخدم بنجاح!');
                $this->reset(['userId', 'amount', 'notes', 'customerName', 'clientCode', 'clientNumber', 'clientNationalNumber']);
                return;
            }
            if ($this->depositType === 'client_wallet') {
                if (!$this->clientId) {
                    session()->flash('message', 'Please select a valid client.');
                    return;
                }
                $client = \App\Models\Domain\Entities\Customer::find($this->clientId);
                if ($client) {
                    $client->balance += $this->amount;
                    $client->save();
                }
                CashTransaction::create([
                    'customer_name' => $this->clientName,
                    'customer_code' => $this->clientCode,
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => 'Completed',
                    'transaction_date_time' => now(),
                    'depositor_national_id' => $this->depositorNationalId,
                    'depositor_mobile_number' => $this->depositorMobileNumber,
                    'agent_id' => $agent->id,
                ]);
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                session()->flash('message', 'تم حفظ إيداع محفظة العميل بنجاح!');
                $this->reset(['clientId', 'clientName', 'clientMobile', 'clientCode', 'clientBalance', 'clientSearch', 'amount', 'notes', 'userId', 'customerName', 'clientNumber', 'clientNationalNumber', 'depositorNationalId', 'depositorMobileNumber']);
                return;
            }
            if ($this->depositType === 'admin') {
                CashTransaction::create([
                    'customer_name' => 'اداري',
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => 'Completed',
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                ]);
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                session()->flash('message', 'تم حفظ إيداع اداري بنجاح!');
                $this->reset(['amount', 'notes']);
                return;
            }
            if ($this->depositType === 'direct') {
                CashTransaction::create([
                    'customer_name' => $this->customerName,
                    'amount' => $this->amount,
                    'notes' => $this->notes,
                    'safe_id' => $safeId,
                    'transaction_type' => $transactionType,
                    'status' => 'Completed',
                    'transaction_date_time' => now(),
                    'agent_id' => $agent->id,
                ]);
                $safe = \App\Models\Domain\Entities\Safe::find($safeId);
                if ($safe) {
                    $safe->increment('current_balance', $this->amount);
                }
                session()->flash('message', 'تم حفظ الإيداع المباشر بنجاح!');
                $this->reset(['customerName', 'amount', 'notes', 'userId', 'clientCode', 'clientNumber', 'clientNationalNumber']);
                return;
            }
        } catch (\Exception $e) {
            session()->flash('message', 'خطأ: ' . $e->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.transactions.deposit');
    }
}
