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

    public function mount($transactionId = null, $cashTransactionId = null)
    {
        // Handle both parameter names
        if ($transactionId) {
            $this->transactionId = $transactionId;
        } elseif ($cashTransactionId) {
            $this->cashTransactionId = $cashTransactionId;
        }
        
        // Try to find regular transaction first
        if ($this->transactionId) {
            $this->transaction = Transaction::with(['agent', 'branch', 'line', 'safe'])->find($this->transactionId);
            if ($this->transaction) {
                $this->transactionType = 'transaction';
                return;
            }
        }
        
        // Try to find cash transaction
        if ($this->cashTransactionId) {
            $this->cashTransaction = CashTransaction::with(['agent', 'safe.branch'])->find($this->cashTransactionId);
            if ($this->cashTransaction) {
                $this->transactionType = 'cash_transaction';
                return;
            }
        }
        
        // If we have a transactionId but no cashTransactionId, try cash transaction with that ID
        if ($this->transactionId && !$this->transaction) {
            $this->cashTransaction = CashTransaction::with(['agent', 'safe.branch'])->find($this->transactionId);
            if ($this->cashTransaction) {
                $this->transactionType = 'cash_transaction';
                return;
            }
        }
        
        // If we have a cashTransactionId but no transactionId, try regular transaction with that ID
        if ($this->cashTransactionId && !$this->cashTransaction) {
            $this->transaction = Transaction::with(['agent', 'branch', 'line', 'safe'])->find($this->cashTransactionId);
            if ($this->transaction) {
                $this->transactionType = 'transaction';
                return;
            }
        }
        
        // If we still haven't found anything, abort
        abort(404, 'Transaction not found');
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