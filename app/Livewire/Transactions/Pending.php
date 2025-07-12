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
        $pendingTransactions = array_map(function ($transaction) {
            if ($transaction['transaction_type'] === 'withdrawal' && $transaction['line_id']) {
                $transaction['line'] = $this->lineRepository->findById($transaction['line_id']);
            }
            // Add agent_name for transactions
            if (isset($transaction['agent_id'])) {
                $agent = \App\Domain\Entities\User::find($transaction['agent_id']);
                $transaction['agent_name'] = $agent ? $agent->name : '';
            }
            $transaction['source'] = 'transactions';
            return $transaction;
        }, $transactions);

        // Add pending withdrawals from cash_transactions (only pending status)
        $pendingCashWithdrawals = \App\Models\Domain\Entities\CashTransaction::where('status', 'pending')
            ->where('transaction_type', 'Withdrawal')
            ->get()
            ->map(function ($tx) {
                $arr = $tx->toArray();
                $arr['source'] = 'cash_transactions';
                // Add agent_name for cash_transactions
                if (isset($arr['agent_id'])) {
                    $agent = \App\Domain\Entities\User::find($arr['agent_id']);
                    $arr['agent_name'] = $agent ? $agent->name : '';
                }
                return $arr;
            })->toArray();

        // Filter out any non-pending transactions and merge
        $this->pendingTransactions = array_merge($pendingTransactions, $pendingCashWithdrawals);
    }

    public function approve($transactionId)
    {
        // Try to approve both Transaction and CashTransaction
        $transaction = \App\Models\Domain\Entities\Transaction::find($transactionId);
        if ($transaction && $transaction->status === 'Pending') {
            // Update status to completed
            $transaction->status = 'completed';
            $transaction->save();
            // Update safe balance
            $safe = \App\Models\Domain\Entities\Safe::find($transaction->safe_id);
            if ($safe) {
                $safe->current_balance -= $transaction->amount;
                $safe->save();
            }
            session()->flash('message', 'Transaction approved successfully!');
            $this->loadPendingTransactions();
            $this->dispatch('$refresh');
            return;
        }
        
        // Try CashTransaction (for withdrawals)
        $cashTransaction = \App\Models\Domain\Entities\CashTransaction::find($transactionId);
        if ($cashTransaction && $cashTransaction->status === 'pending' && $cashTransaction->transaction_type === 'Withdrawal') {
            // Branch withdrawal logic
            if ($cashTransaction->destination_branch_id && $cashTransaction->destination_safe_id) {
                // Deduct from selected branch's safe (destination_safe_id)
                $sourceSafe = \App\Models\Domain\Entities\Safe::find($cashTransaction->destination_safe_id);
                if ($sourceSafe) {
                    if (($sourceSafe->current_balance - abs($cashTransaction->amount)) < 0) {
                        session()->flash('error', 'Insufficient balance in source branch safe. Available: ' . number_format($sourceSafe->current_balance, 2) . ' EGP, Required: ' . number_format(abs($cashTransaction->amount), 2) . ' EGP');
                        return;
                    }
                    $sourceSafe->current_balance -= abs($cashTransaction->amount);
                    $sourceSafe->save();
                }
                // Add to agent's branch safe (safe_id)
                $destSafe = \App\Models\Domain\Entities\Safe::find($cashTransaction->safe_id);
                if ($destSafe) {
                    $destSafe->current_balance += abs($cashTransaction->amount);
                    $destSafe->save();
                }
                $cashTransaction->status = 'completed';
                $cashTransaction->save();
                session()->flash('message', 'Branch withdrawal approved and balances updated!');
                $this->loadPendingTransactions();
                $this->dispatch('$refresh');
                return;
            }
            
            // Regular withdrawal logic
            // Deduct from client wallet if applicable
            if ($cashTransaction->customer_code) {
                $client = \App\Models\Domain\Entities\Customer::where('customer_code', $cashTransaction->customer_code)->first();
                if ($client) {
                    if (($client->balance - abs($cashTransaction->amount)) < 0) {
                        session()->flash('error', 'Insufficient client balance. Available: ' . number_format($client->balance, 2) . ' EGP, Required: ' . number_format(abs($cashTransaction->amount), 2) . ' EGP');
                        return;
                    }
                    $client->balance -= abs($cashTransaction->amount);
                    $client->save();
                }
            }
            
            // Check if this is an expense withdrawal (has destination_branch_id but no customer_code)
            if ($cashTransaction->destination_branch_id && !$cashTransaction->customer_code && str_contains($cashTransaction->customer_name, 'Expense:')) {
                // Expense withdrawal logic - deduct from safe
                $safe = \App\Models\Domain\Entities\Safe::find($cashTransaction->safe_id);
                if ($safe) {
                    if (($safe->current_balance - abs($cashTransaction->amount)) < 0) {
                        session()->flash('error', 'Insufficient safe balance for expense withdrawal. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format(abs($cashTransaction->amount), 2) . ' EGP');
                        return;
                    }
                    $safe->current_balance -= abs($cashTransaction->amount);
                    $safe->save();
                }
                
                // Update status to completed
                $cashTransaction->status = 'completed';
                $cashTransaction->save();
                
                session()->flash('message', 'Expense withdrawal approved and safe balance updated!');
                $this->loadPendingTransactions();
                $this->dispatch('$refresh');
                return;
            }
            
            // Deduct from safe
            $safe = \App\Models\Domain\Entities\Safe::find($cashTransaction->safe_id);
            if ($safe) {
                if (($safe->current_balance - abs($cashTransaction->amount)) < 0) {
                    session()->flash('error', 'Insufficient safe balance. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format(abs($cashTransaction->amount), 2) . ' EGP');
                    return;
                }
                $safe->current_balance -= abs($cashTransaction->amount);
                $safe->save();
            }
            
            // Update status to completed
            $cashTransaction->status = 'completed';
            $cashTransaction->save();
            
            session()->flash('message', 'Withdrawal approved and balances updated!');
            $this->loadPendingTransactions();
            $this->dispatch('$refresh');
            return;
        }
        
        session()->flash('error', 'Failed to approve transaction: Transaction not found or already processed.');
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
            $this->dispatch('$refresh');
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
