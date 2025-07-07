<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Application\UseCases\RegisterTransaction;
use App\Application\UseCases\FindClient;
use App\Application\UseCases\UpdateClient;
use App\Application\UseCases\GetTransferHistory;
use App\Application\UseCases\GetWalletBalance;
use App\Notifications\SupervisorDiscountNotification;
use App\Notifications\AdminWalletApprovalNotification;

class Send extends Component
{
    public $client_code;
    public $phone_number;
    public $client_name;
    public $gender;
    public $to_client;
    public $amount = 0;
    public $commission = 0;
    public $discount = 0;
    public $collect_from_wallet = false;
    public $line_type = 'internal';
    public $warning = '';
    public $employee_name;
    public $branch_name;
    public $client_found = false;
    public $client_id;
    public $previous_recipients = [];
    public $allCustomers = [];
    public $customerSuggestions = [];
    public $searchTerm = '';
    public $toClientSearch = '';
    public $toClientSuggestions = [];
    public $toClientId = null;
    public $toClientName = '';
    public $toClientPhone = '';
    public $toClientCode = '';
    public $toClientGender = '';

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
        $this->allCustomers = \App\Models\Domain\Entities\Customer::select('id', 'name', 'mobile_number', 'customer_code', 'gender')->get()->toArray();
    }

    public function updatedClientName($value)
    {
        $this->customerSuggestions = collect($this->allCustomers)
            ->filter(fn($c) => stripos($c['name'], $value) !== false)
            ->take(5)
            ->values()
            ->toArray();
    }

    public function updatedPhoneNumber($value)
    {
        $this->customerSuggestions = collect($this->allCustomers)
            ->filter(fn($c) => stripos($c['mobile_number'], $value) !== false)
            ->take(5)
            ->values()
            ->toArray();
    }

    public function selectCustomer($customerId)
    {
        $customer = collect($this->allCustomers)->firstWhere('id', $customerId);
        if ($customer) {
            $this->client_found = true;
            $this->client_id = $customer['id'];
            $this->client_code = $customer['customer_code'];
            $this->client_name = $customer['name'];
            $this->phone_number = $customer['mobile_number'];
            $this->gender = $customer['gender'];
            $this->customerSuggestions = [];
        }
    }

    public function updatedAmount($value)
    {
        $val = floatval($value);
        if ($val < 1) {
            $this->commission = 0;
        } else {
            $this->commission = max(5, ceil($val / 500) * 5);
        }
    }

    public function updatedToClientSearch($value)
    {
        $this->toClientSuggestions = collect($this->allCustomers)
            ->filter(function($c) use ($value) {
                return stripos($c['name'], $value) !== false ||
                       stripos($c['mobile_number'], $value) !== false ||
                       stripos($c['customer_code'], $value) !== false;
            })
            ->take(5)
            ->values()
            ->toArray();
    }

    public function selectToClient($customerId)
    {
        $customer = collect($this->allCustomers)->firstWhere('id', $customerId);
        if ($customer) {
            $this->toClientId = $customer['id'];
            $this->toClientName = $customer['name'];
            $this->toClientPhone = $customer['mobile_number'];
            $this->toClientCode = $customer['customer_code'];
            $this->toClientGender = $customer['gender'];
            $this->to_client = $customer['name'];
            $this->toClientSuggestions = [];
        }
    }

    public function createToClient()
    {
        $newClient = new \App\Models\Domain\Entities\Customer();
        $newClient->name = $this->toClientName ?: $this->to_client;
        $newClient->mobile_number = $this->toClientPhone;
        $newClient->customer_code = $this->toClientCode;
        $newClient->gender = $this->toClientGender ?: 'male';
        $newClient->balance = 0;
        $newClient->is_client = true;
        $newClient->branch_id = Auth::user()->branch_id;
        $newClient->save();
        $this->toClientId = $newClient->id;
        $this->to_client = $newClient->name;
    }

    public function addTransaction()
    {
        $this->validate();
        $walletBalance = app(GetWalletBalance::class)->forClient($this->client_id);
        $lineBalance = 1000000; // TODO: Replace with actual line balance fetch if needed
        if ($this->amount > $walletBalance) {
            $this->warning = 'Amount exceeds wallet balance!';
            return;
        }
        if ($this->amount > $lineBalance) {
            $this->warning = 'Amount exceeds line balance!';
            return;
        }
        $pending = false;
        if ($this->discount > 0) {
            Notification::route('mail', 'supervisor@example.com')->notify(new SupervisorDiscountNotification($this->client_id, $this->amount, $this->discount));
            $pending = true;
        }
        if ($this->collect_from_wallet) {
            Notification::route('mail', 'admin@example.com')->notify(new AdminWalletApprovalNotification($this->client_id, $this->amount));
            $pending = true;
        }
        // If client not found, register new client
        if (!$this->client_id) {
            $newClient = new \App\Models\Domain\Entities\Customer();
            $newClient->name = $this->client_name;
            $newClient->mobile_number = $this->phone_number;
            $newClient->customer_code = $this->client_code;
            $newClient->gender = $this->gender;
            $newClient->balance = 0;
            $newClient->is_client = true;
            $newClient->branch_id = Auth::user()->branch_id;
            $newClient->save();
            $this->client_id = $newClient->id;
        }
        // Ensure recipient exists
        if (!$this->toClientId) {
            $this->createToClient();
        }
        $this->commission = max(5, ceil($this->amount / 500) * 5);
        $transaction = app(RegisterTransaction::class)->execute([
            'client_id' => $this->client_id,
            'to_client' => $this->toClientId,
            'amount' => $this->amount,
            'commission' => $this->commission,
            'discount' => $this->discount,
            'pending' => $pending,
            'line_type' => $this->line_type,
            'type' => 'send',
        ]);
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
        // Always recalculate commission for robustness
        $val = floatval($this->amount);
        if ($val < 1) {
            $this->commission = 0;
        } else {
            $this->commission = max(5, ceil($val / 500) * 5);
        }
        return view('livewire.transactions.send', [
            'previous_recipients' => $this->previous_recipients,
            'employee_name' => $this->employee_name,
            'branch_name' => $this->branch_name,
            'warning' => $this->warning,
        ]);
    }
} 