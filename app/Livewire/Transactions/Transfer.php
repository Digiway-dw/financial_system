<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use App\Application\UseCases\RegisterTransaction;
use App\Application\UseCases\FindClient;
use App\Application\UseCases\UpdateClient;
use App\Application\UseCases\GetTransferHistory;
use App\Application\UseCases\ApproveTransaction;
use App\Application\UseCases\GetWalletBalance;
use App\Application\UseCases\GetLineBalance;
use App\Notifications\SupervisorDiscountNotification;
use App\Notifications\AdminWalletApprovalNotification;

class Transfer extends Component
{
    public $client_code;
    public $phone_number;
    public $client_name;
    public $gender;
    public $to_client;
    public $amount;
    public $commission = 0;
    public $discount = 0;
    public $collect_from_wallet = false;
    public $line_type = 'internal';
    public $search;
    public $date_from;
    public $date_to;
    public $transaction_number;
    public $transferred_to;
    public $history = [];
    public $pending_approval = false;
    public $warning = '';
    public $employee_name;
    public $branch_name;
    public $client_found = false;
    public $client_id;
    public $previous_recipients = [];

    protected $rules = [
        'client_code' => 'nullable|string',
        'phone_number' => 'required|string',
        'client_name' => 'required|string',
        'gender' => 'required|in:male,female',
        'to_client' => 'required|string',
        'amount' => 'required|numeric|min:1',
        'discount' => 'nullable|numeric|min:0',
        'line_type' => 'required|in:internal,external',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->employee_name = $user->name;
        $this->branch_name = $user->branch->name ?? '';
        $this->previous_recipients = app(GetTransferHistory::class)->getRecipientsForUser($user->id);
    }

    public function updatedPhoneNumber()
    {
        $client = app(FindClient::class)->byPhone($this->phone_number);
        if ($client) {
            $this->client_found = true;
            $this->client_id = $client->id;
            $this->client_code = $client->code;
            $this->client_name = $client->name;
            $this->gender = $client->gender;
        } else {
            $this->client_found = false;
            $this->client_name = '';
            $this->gender = '';
        }
    }

    public function updatedClientCode()
    {
        $client = app(FindClient::class)->byCode($this->client_code);
        if ($client) {
            $this->client_found = true;
            $this->client_id = $client->id;
            $this->client_name = $client->name;
            $this->phone_number = $client->phone;
            $this->gender = $client->gender;
        } else {
            $this->client_found = false;
            $this->client_name = '';
            $this->phone_number = '';
            $this->gender = '';
        }
    }

    public function updatedAmount()
    {
        $this->commission = ceil($this->amount / 500) * 5;
    }

    public function addTransaction()
    {
        $this->validate();
        $walletBalance = app(GetWalletBalance::class)->forClient($this->client_id);
        $lineBalance = app(GetLineBalance::class)->forClient($this->client_id, $this->line_type);
        $isSend = $this->collect_from_wallet;
        $exceeds = false;
        if ($isSend && $this->amount > $walletBalance) {
            $this->warning = 'Amount exceeds wallet balance!';
            $exceeds = true;
        }
        if (!$isSend && $this->amount > $lineBalance) {
            $this->warning = 'Amount exceeds line balance!';
            $exceeds = true;
        }
        if ($exceeds) return;

        $pending = false;
        if ($this->discount > 0) {
            Notification::route('mail', 'supervisor@example.com')->notify(new SupervisorDiscountNotification($this->client_id, $this->amount, $this->discount));
            $pending = true;
        }
        if ($this->collect_from_wallet) {
            Notification::route('mail', 'admin@example.com')->notify(new AdminWalletApprovalNotification($this->client_id, $this->amount));
            $pending = true;
        }
        $transaction = app(RegisterTransaction::class)->execute([
            'client_id' => $this->client_id,
            'to_client' => $this->to_client,
            'amount' => $this->amount,
            'commission' => $this->commission,
            'discount' => $this->discount,
            'pending' => $pending,
            'line_type' => $this->line_type,
            'type' => $isSend ? 'send' : 'receive',
        ]);
        // Update or insert client
        app(UpdateClient::class)->execute([
            'id' => $this->client_id,
            'name' => $this->client_name,
            'phone' => $this->phone_number,
            'gender' => $this->gender,
        ]);
        $this->reset(['amount', 'commission', 'discount', 'collect_from_wallet', 'warning']);
        session()->flash('message', $pending ? 'Transaction pending approval.' : 'Transaction added successfully.');
    }

    public function render()
    {
        return view('livewire.transactions.transfer', [
            'previous_recipients' => $this->previous_recipients,
            'employee_name' => $this->employee_name,
            'branch_name' => $this->branch_name,
            'warning' => $this->warning,
        ]);
    }
} 