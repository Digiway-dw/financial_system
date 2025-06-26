<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\ListPendingTransactions;
use App\Application\UseCases\ApproveTransaction;
use App\Application\UseCases\RejectTransaction;
use Livewire\Component;

class Pending extends Component
{
    public array $pendingTransactions;

    private ListPendingTransactions $listPendingTransactionsUseCase;
    private ApproveTransaction $approveTransactionUseCase;
    private RejectTransaction $rejectTransactionUseCase;

    public function boot(
        ListPendingTransactions $listPendingTransactionsUseCase,
        ApproveTransaction $approveTransactionUseCase,
        RejectTransaction $rejectTransactionUseCase
    )
    {
        $this->listPendingTransactionsUseCase = $listPendingTransactionsUseCase;
        $this->approveTransactionUseCase = $approveTransactionUseCase;
        $this->rejectTransactionUseCase = $rejectTransactionUseCase;
    }

    public function mount()
    {
        $this->loadPendingTransactions();
    }

    public function loadPendingTransactions()
    {
        $this->pendingTransactions = $this->listPendingTransactionsUseCase->execute();
    }

    public function approve(string $transactionId)
    {
        try {
            $this->approveTransactionUseCase->execute($transactionId);
            session()->flash('message', 'Transaction approved successfully.');
            $this->loadPendingTransactions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve transaction: ' . $e->getMessage());
        }
    }

    public function reject(string $transactionId)
    {
        try {
            $this->rejectTransactionUseCase->execute($transactionId);
            session()->flash('message', 'Transaction rejected successfully.');
            $this->loadPendingTransactions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to reject transaction: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.transactions.pending', [
            'pendingTransactions' => $this->pendingTransactions,
        ]);
    }
}
