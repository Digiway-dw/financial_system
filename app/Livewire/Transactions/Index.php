<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\ListTransactions;
use App\Application\UseCases\DeleteTransaction;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    public array $transactions;

    private ListTransactions $listTransactionsUseCase;
    private DeleteTransaction $deleteTransactionUseCase;

    public function boot(ListTransactions $listTransactionsUseCase, DeleteTransaction $deleteTransactionUseCase)
    {
        $this->listTransactionsUseCase = $listTransactionsUseCase;
        $this->deleteTransactionUseCase = $deleteTransactionUseCase;
    }

    public function mount()
    {
        // Authorization check for viewing transactions is now handled inside ListTransactions use case
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transactions = $this->listTransactionsUseCase->execute();
    }

    public function deleteTransaction(string $transactionId)
    {
        Gate::authorize('edit-all-data'); // Only admin can delete transactions
        try {
            $this->deleteTransactionUseCase->execute($transactionId);
            session()->flash('message', 'Transaction deleted successfully.');
            $this->loadTransactions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.transactions.index', [
            'transactions' => $this->transactions,
        ]);
    }
}
