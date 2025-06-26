<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\ListTransactions;
use App\Application\UseCases\DeleteTransaction;
use Livewire\Component;

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
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $this->transactions = $this->listTransactionsUseCase->execute();
    }

    public function deleteTransaction(string $transactionId)
    {
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
