<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;

class Details extends Component
{
    public $transactionId;
    public $cashTransactionId;
    public $transaction;
    public $cashTransaction;
    public $transactionType; // 'transaction' or 'cash_transaction'

    public function mount($referenceNumber = null)
    {
        // The parameter is now directly the reference number
        
        if (!$referenceNumber) {
            abort(404, 'Transaction reference number not provided');
        }
        
        // Try to find regular transaction by reference number first
        $this->transaction = Transaction::with(['agent', 'branch', 'line', 'safe'])
            ->where('reference_number', $referenceNumber)
            ->first();
            
        if ($this->transaction) {
            $this->transactionType = 'transaction';
            return;
        }
        
        // Try to find cash transaction by reference number
        $this->cashTransaction = CashTransaction::with(['agent', 'safe.branch'])
            ->where('reference_number', $referenceNumber)
            ->first();
            
        if ($this->cashTransaction) {
            $this->transactionType = 'cash_transaction';
            return;
        }
        
        // If we still haven't found anything, abort
        abort(404, 'Transaction not found with reference number: ' . $referenceNumber);
    }

    public function render()
    {
        return view('livewire.transactions.details', [
            'transaction' => $this->transaction,
            'cashTransaction' => $this->cashTransaction,
            'transactionType' => $this->transactionType,
        ]);
    }
} 