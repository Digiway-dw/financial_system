<?php

namespace App\Livewire\Transactions;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\CashTransaction;

class WaitingApproval extends Component
{
    public $transactionId;
    public $transactionType; // 'transaction' or 'cash_transaction'
    public $transaction;
    public $cashTransaction;
    public $isApproved = false;
    public $showContactMessage = false;

    public function mount($transactionId = null, $cashTransaction = null)
    {
        if ($transactionId) {
            $this->transactionId = $transactionId;
            $this->transactionType = 'transaction';
            $this->transaction = Transaction::find($transactionId);
        } elseif ($cashTransaction) {
            $this->transactionId = $cashTransaction;
            $this->transactionType = 'cash_transaction';
            $this->cashTransaction = CashTransaction::find($cashTransaction);
        }
    }

    public function checkApprovalStatus()
    {
        if ($this->transactionType === 'transaction' && $this->transaction) {
            $this->transaction->refresh();
            if ($this->transaction->status === 'Completed') {
                $this->isApproved = true;
                $this->showContactMessage = false;
            } else {
                $this->isApproved = false;
                $this->showContactMessage = true;
            }
        } elseif ($this->transactionType === 'cash_transaction' && $this->cashTransaction) {
            $this->cashTransaction->refresh();
            if ($this->cashTransaction->status === 'completed') {
                $this->isApproved = true;
                $this->showContactMessage = false;
            } else {
                $this->isApproved = false;
                $this->showContactMessage = true;
            }
        }
    }

    public function goToReceipt()
    {
        if ($this->transactionType === 'transaction' && $this->transaction) {
            return redirect()->route('transactions.receipt', ['transaction' => $this->transaction->id]);
        } elseif ($this->transactionType === 'cash_transaction' && $this->cashTransaction) {
            return redirect()->route('cash-transactions.receipt', ['cashTransaction' => $this->cashTransaction->id]);
        }
    }

    public function goToTransactions()
    {
        if ($this->transactionType === 'transaction') {
            return redirect()->route('transactions.index');
        } else {
            return redirect()->route('transactions.cash.index');
        }
    }

    public function render()
    {
        return view('livewire.transactions.waiting-approval');
    }
} 