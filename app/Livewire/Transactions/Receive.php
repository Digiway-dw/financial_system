<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Application\UseCases\RegisterTransaction;
use App\Application\UseCases\FindClient;
use App\Application\UseCases\UpdateClient;
use App\Application\UseCases\GetLineBalance;

class Receive extends Component
{
    public $client_code;
    public $phone_number;
    public $client_name;
    public $gender;
    public $to_client;
    public $amount;
    public $commission = 0;
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

    protected $rules = [
        'client_code' => 'nullable|string',
        'phone_number' => 'required|string',
        'client_name' => 'required|string',
        'gender' => 'required|in:male,female',
        'to_client' => 'required|string',
        'amount' => 'required|numeric|min:1',
        'line_type' => 'required|in:internal,external',
    ];

    public function mount()
    {
        $user = Auth::user();
        $this->employee_name = $user->name;
        $this->branch_name = $user->branch->name ?? '';
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

    public function updatedAmount()
    {
        $this->commission = ceil($this->amount / 500) * 5;
    }

    public function addTransaction()
    {
        $this->validate();
        $lineBalance = app(GetLineBalance::class)->forClient($this->client_id, $this->line_type);
        if ($this->amount > $lineBalance) {
            $this->warning = 'Amount exceeds line balance!';
            return;
        }
        $transaction = app(RegisterTransaction::class)->execute([
            'client_id' => $this->client_id,
            'to_client' => $this->to_client,
            'amount' => $this->amount,
            'commission' => $this->commission,
            'discount' => 0,
            'pending' => false,
            'line_type' => $this->line_type,
            'type' => 'receive',
        ]);
        app(UpdateClient::class)->execute([
            'id' => $this->client_id,
            'name' => $this->client_name,
            'phone' => $this->phone_number,
            'gender' => $this->gender,
        ]);
        $this->reset(['amount', 'commission', 'warning']);
        session()->flash('message', 'Transaction added successfully.');
    }

    public function render()
    {
        return view('livewire.transactions.receive', [
            'employee_name' => $this->employee_name,
            'branch_name' => $this->branch_name,
            'warning' => $this->warning,
        ]);
    }
} 