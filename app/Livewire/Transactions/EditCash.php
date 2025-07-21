<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Models\Domain\Entities\CashTransaction;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Branch;

class EditCash extends Component
{
    public $cashTransaction;
    public $cashTransactionId;

    public $customerName = '';
    public $customerCode = '';
    public $amount = 0.00;
    public $notes = '';
    public $transactionType = 'Withdrawal';
    public $status = 'pending';
    public $safeId = '';
    public $referenceNumber = '';
    public $branches = [];
    public $safes = [];

    public function mount($cashTransactionId)
    {
        $this->cashTransactionId = $cashTransactionId;
        $this->cashTransaction = CashTransaction::find($cashTransactionId);
        if (!$this->cashTransaction) {
            abort(404);
        }
        $user = Auth::user();
        if (!($user->hasRole('admin') || $user->hasRole('general_supervisor'))) {
            abort(403, 'Unauthorized action.');
        }
        $this->customerName = $this->cashTransaction->customer_name;
        $this->customerCode = $this->cashTransaction->customer_code;
        $this->amount = $this->cashTransaction->amount;
        $this->notes = $this->cashTransaction->notes;
        $this->transactionType = $this->cashTransaction->transaction_type;
        $this->status = $this->cashTransaction->status;
        $this->safeId = $this->cashTransaction->safe_id;
        $this->referenceNumber = $this->cashTransaction->reference_number;
        $this->branches = Branch::all();
        $this->safes = Safe::all();
    }

    public function updateCashTransaction()
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerCode' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255',
            'transactionType' => 'required|string|in:Withdrawal,Deposit',
            'status' => 'required|string|in:pending,completed,rejected',
            'safeId' => 'required|exists:safes,id',
        ]);
        $this->cashTransaction->customer_name = $this->customerName;
        $this->cashTransaction->customer_code = $this->customerCode;
        $this->cashTransaction->amount = $this->amount;
        $this->cashTransaction->notes = $this->notes;
        $this->cashTransaction->transaction_type = $this->transactionType;
        $this->cashTransaction->status = $this->status;
        $this->cashTransaction->safe_id = $this->safeId;
        $this->cashTransaction->save();
        session()->flash('message', 'Cash transaction updated successfully.');
        return redirect()->route('transactions.index');
    }

    public function render()
    {
        return view('livewire.transactions.edit-cash');
    }
} 