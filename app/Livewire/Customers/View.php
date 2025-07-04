<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Domain\Entities\Customer;

class View extends Component
{
    public $customer;
    public $totalTransactions = 0;
    public $totalTransferred = 0;
    public $totalCommission = 0;

    public function mount($customerId)
    {
        $customer = Customer::with(['mobileNumbers', 'transactions'])->findOrFail($customerId);
        $this->customer = $customer;
        $this->totalTransactions = $customer->transactions->count();
        $this->totalTransferred = $customer->transactions->where('transaction_type', 'Transfer')->sum('amount');
        $this->totalCommission = $customer->transactions->sum('commission');
    }

    public function render()
    {
        return view('livewire.customers.view', [
            'customer' => $this->customer,
            'totalTransactions' => $this->totalTransactions,
            'totalTransferred' => $this->totalTransferred,
            'totalCommission' => $this->totalCommission,
        ]);
    }
} 