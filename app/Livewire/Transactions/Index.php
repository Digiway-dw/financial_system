<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\ListTransactions;
use App\Application\UseCases\DeleteTransaction;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Index extends Component
{
    public array $transactions;

    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public $customer_code;
    public $receiver_mobile_number;
    public $transfer_line;
    public $amount_from; // Changed from single amount to range
    public $amount_to;   // Added for amount range
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
        // Authorization check for viewing transactions
        if (auth()->user()->hasRole('agent') || auth()->user()->hasRole('trainee')) {
            session()->flash('error', 'You are not authorized to access the transactions page.');
            return redirect()->route('dashboard');
        }
        Gate::authorize('view-transactions');
        
        // Initialize transactions array
        $this->transactions = [];
        
        $this->loadTransactions();
    }

    public function loadTransactions()
    {
        $filters = array_filter([
            'customer_code' => $this->customer_code,
            'receiver_mobile_number' => $this->receiver_mobile_number,
            'transfer_line' => $this->transfer_line,
            'amount_from' => $this->amount_from, // Changed from amount to amount_from
            'amount_to' => $this->amount_to,     // Added amount_to
            'commission' => $this->commission,
            'transaction_type' => $this->transaction_type,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'employee_ids' => !empty($this->employee_ids) ? $this->employee_ids : null,
            'branch_ids' => !empty($this->branch_ids) ? $this->branch_ids : null,
            'reference_number' => $this->reference_number,
            'sortField' => $this->sortField,
            'sortDirection' => $this->sortDirection,
        ], function($value) {
            return $value !== null && $value !== '';
        });
        
        $this->transactions = $this->listTransactionsUseCase->execute($filters);
    }

    public function filter()
    {
        $this->loadTransactions();
    }

    // Add updated hook for real-time filtering
    public function updated($propertyName)
    {
        // If any filter property is updated, reset pagination to first page
        if (in_array($propertyName, [
            'customer_code',
            'receiver_mobile_number',
            'transfer_line',
            'amount_from',
            'amount_to',
            'commission',
            'transaction_type',
            'start_date',
            'end_date',
            'employee_ids',
            'branch_ids',
            'reference_number'
        ])) {
            // Don't auto-filter on every keystroke to avoid too many requests
            // User will need to click the filter button
        }
    }

    public function resetFilters()
    {
        $this->customer_code = null;
        $this->receiver_mobile_number = null;
        $this->transfer_line = null;
        $this->amount_from = null;
        $this->amount_to = null;
        $this->commission = null;
        $this->transaction_type = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->employee_ids = [];
        $this->branch_ids = [];
        $this->reference_number = null;
        $this->loadTransactions();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
        $this->loadTransactions();
    }

    public function deleteTransaction(string $transactionId)
    {
        // Find the transaction in the list to determine its source
        $transaction = collect($this->transactions)->firstWhere('id', $transactionId);
        if (isset($transaction['source_table']) && $transaction['source_table'] === 'cash_transactions') {
            $this->deleteCashTransaction($transactionId);
            return;
        }
        Gate::authorize('edit-all-data'); // Only admin can delete transactions
        try {
            $this->deleteTransactionUseCase->execute($transactionId);
            session()->flash('message', 'Transaction deleted successfully.');
            $this->loadTransactions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete transaction: ' . $e->getMessage());
        }
    }

    public function deleteCashTransaction(string $cashTransactionId)
    {
        \App\Models\Domain\Entities\CashTransaction::find($cashTransactionId)?->delete();
        $this->loadTransactions();
    }

    public function render()
    {
        return view('livewire.transactions.index', [
            'transactions' => $this->transactions,
        ]);
    }
}
