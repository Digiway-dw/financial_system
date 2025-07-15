<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\ListTransactions;
use App\Application\UseCases\DeleteTransaction;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    public array $transactions;

    public $customer_code;
    public $receiver_mobile;
    public $transfer_line;
    public $amount;
    public $commission;
    public $transaction_type;
    public $start_date;
    public $end_date;
    public $employee_ids = [];
    public $branch_ids = [];
    public $reference_number;

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
        $filters = [
            'customer_code' => $this->customer_code,
            'receiver_mobile' => $this->receiver_mobile,
            'transfer_line' => $this->transfer_line,
            'amount' => $this->amount,
            'commission' => $this->commission,
            'transaction_type' => $this->transaction_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'employee_ids' => $this->employee_ids,
            'branch_ids' => $this->branch_ids,
            'reference_number' => $this->reference_number,
        ];
        $this->transactions = $this->listTransactionsUseCase->execute($filters);
    }

    public function filter()
    {
        $this->loadTransactions();
    }

    public function resetFilters()
    {
        $this->customer_code = null;
        $this->receiver_mobile = null;
        $this->transfer_line = null;
        $this->amount = null;
        $this->commission = null;
        $this->transaction_type = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->employee_ids = [];
        $this->branch_ids = [];
        $this->loadTransactions();
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
