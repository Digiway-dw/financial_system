<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Domain\Entities\Customer;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;

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
        // Fetch ordinary transactions
        $ordinary = Transaction::where('customer_mobile_number', $customer->mobile_number)
            ->orWhere('customer_code', $customer->customer_code)
            ->get();
        // Fetch cash transactions by customer_code only
        $cash = CashTransaction::where('customer_code', $customer->customer_code)->get();
        // Merge and sort
        $allTransactions = collect($ordinary)->merge($cash)->sortByDesc(function($t) {
            return $t->transaction_date_time ?? $t->created_at;
        })->values();
        $this->customer->transactions = $allTransactions;
        $this->totalTransactions = $allTransactions->count();
        $this->totalTransferred = $allTransactions->where('transaction_type', 'Transfer')->sum('amount');
        $this->totalCommission = $allTransactions->sum(function($t) { return $t->commission ?? 0; });
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