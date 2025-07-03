<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\ListPendingTransactions;
use App\Application\UseCases\ApproveTransaction;
use App\Application\UseCases\RejectTransaction;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Domain\Interfaces\LineRepository;

class Pending extends Component
{
    public array $pendingTransactions;

    private ListPendingTransactions $listPendingTransactionsUseCase;
    private ApproveTransaction $approveTransactionUseCase;
    private RejectTransaction $rejectTransactionUseCase;
    private LineRepository $lineRepository;

    public function boot(
        ListPendingTransactions $listPendingTransactionsUseCase,
        ApproveTransaction $approveTransactionUseCase,
        RejectTransaction $rejectTransactionUseCase,
        LineRepository $lineRepository
    )
    {
        $this->listPendingTransactionsUseCase = $listPendingTransactionsUseCase;
        $this->approveTransactionUseCase = $approveTransactionUseCase;
        $this->rejectTransactionUseCase = $rejectTransactionUseCase;
        $this->lineRepository = $lineRepository;
    }

    public function mount()
    {
        // Authorization check
        $user = Auth::user();
        if ($user->can('view-pending-transactions')) {
            // User has permission to view pending transactions generally
            $this->loadPendingTransactions();
        } elseif ($user->can('view-own-branch-data')) {
            // Branch Manager can view pending transactions for their branch
            $this->loadPendingTransactions($user->branch_id);
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function loadPendingTransactions(?int $branchId = null)
    {
        $transactions = $this->listPendingTransactionsUseCase->execute($branchId);
        
        // Eager load line relationship if it's a withdrawal and line_id exists
        $this->pendingTransactions = array_map(function ($transaction) {
            if ($transaction->transaction_type === 'Withdrawal' && $transaction->line_id) {
                $transaction->line = $this->lineRepository->findById($transaction->line_id);
            }
            return $transaction;
        }, $transactions);
    }

    public function approve(string $transactionId)
    {
        // Authorization check for approving transactions
        $user = Auth::user();
        $transaction = $this->listPendingTransactionsUseCase->findById($transactionId); // Assuming a findById for transaction exists in use case or repository

        if (!$transaction) {
            session()->flash('error', 'Transaction not found.');
            return;
        }

        if ($user->can('approve-all-requests') || $user->can('approve-pending-transactions')) {
            // Admin/Auditor can approve any pending transaction
        } elseif ($user->can('approve-own-branch-transactions') && $user->branch_id === $transaction['branch_id']) {
            // Branch Manager can approve pending transactions in their own branch
        } else {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->approveTransactionUseCase->execute($transactionId, Auth::user()->id);
            session()->flash('message', 'Transaction approved successfully.');
            $this->loadPendingTransactions();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to approve transaction: ' . $e->getMessage());
        }
    }

    public function reject(string $transactionId)
    {
        // Authorization check for rejecting transactions
        $user = Auth::user();
        $transaction = $this->listPendingTransactionsUseCase->findById($transactionId); // Assuming a findById for transaction exists in use case or repository

        if (!$transaction) {
            session()->flash('error', 'Transaction not found.');
            return;
        }

        if ($user->can('approve-all-requests') || $user->can('approve-pending-transactions')) {
            // Admin/Auditor can reject any pending transaction
        } elseif ($user->can('approve-own-branch-transactions') && $user->branch_id === $transaction['branch_id']) {
            // Branch Manager can reject pending transactions in their own branch
        } else {
            abort(403, 'Unauthorized action.');
        }

        try {
            $this->rejectTransactionUseCase->execute($transactionId, Auth::user()->id, 'Rejected by ' . $user->name . ''); // Assuming reject takes a reason
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
