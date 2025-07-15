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
    public $isRejected = false;
    public $rejectedBy = null;
    public $rejectionReason = null;

    public function mount($transactionId = null, $cashTransaction = null)
    {
        if ($transactionId) {
            $this->transactionId = $transactionId;
            $this->transactionType = 'transaction';
            $this->transaction = Transaction::find($transactionId);
            if ($this->transaction && strtolower($this->transaction->status) === 'rejected') {
                $this->isRejected = true;
                $this->rejectedBy = optional(\App\Domain\Entities\User::find($this->transaction->rejected_by))->name;
                $this->rejectionReason = $this->transaction->rejection_reason;
            }
        } elseif ($cashTransaction) {
            $this->transactionId = $cashTransaction;
            $this->transactionType = 'cash_transaction';
            $this->cashTransaction = CashTransaction::find($cashTransaction);
            if ($this->cashTransaction && strtolower($this->cashTransaction->status) === 'rejected') {
                $this->isRejected = true;
                $this->rejectedBy = optional(\App\Domain\Entities\User::find($this->cashTransaction->rejected_by))->name;
                $this->rejectionReason = $this->cashTransaction->rejection_reason;
            }
        }
    }

    public function checkApprovalStatus()
    {
        if ($this->transactionType === 'transaction' && $this->transaction) {
            $this->transaction->refresh();
            if (strtolower($this->transaction->status) === 'rejected') {
                $this->isRejected = true;
                $this->rejectedBy = optional(\App\Domain\Entities\User::find($this->transaction->rejected_by))->name;
                $this->rejectionReason = $this->transaction->rejection_reason;
                $this->isApproved = false;
                $this->showContactMessage = false;
            } elseif ($this->transaction->status === 'Completed') {
                $this->isApproved = true;
                $this->showContactMessage = false;
                $this->isRejected = false;
            } else {
                $this->isApproved = false;
                $this->showContactMessage = true;
                $this->isRejected = false;
            }
        } elseif ($this->transactionType === 'cash_transaction' && $this->cashTransaction) {
            $this->cashTransaction->refresh();
            if (strtolower($this->cashTransaction->status) === 'rejected') {
                $this->isRejected = true;
                $this->rejectedBy = optional(\App\Domain\Entities\User::find($this->cashTransaction->rejected_by))->name;
                $this->rejectionReason = $this->cashTransaction->rejection_reason;
                $this->isApproved = false;
                $this->showContactMessage = false;
            } elseif ($this->cashTransaction->status === 'completed') {
                $this->isApproved = true;
                $this->showContactMessage = false;
                $this->isRejected = false;
            } else {
                $this->isApproved = false;
                $this->showContactMessage = true;
                $this->isRejected = false;
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