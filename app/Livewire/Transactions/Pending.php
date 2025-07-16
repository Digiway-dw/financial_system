<?php

namespace App\Livewire\Transactions;

use App\Application\UseCases\ListPendingTransactions;
use App\Application\UseCases\ApproveTransaction;
use App\Application\UseCases\RejectTransaction;
use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    ) {
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

    public function loadPendingTransactions()
    {
        // Transactions that require approval: status 'Pending' and deduction > 0
        $pendingTransactions = \App\Models\Domain\Entities\Transaction::where('status', 'Pending')
            ->where('deduction', '>', 0)
            ->get()
            ->map(function ($tx) {
                $arr = $tx->toArray();
                $arr['source'] = 'transactions';
                // Add agent_name for transactions
                if (isset($arr['agent_id'])) {
                    $agent = \App\Domain\Entities\User::find($arr['agent_id']);
                    $arr['agent_name'] = $agent ? $agent->name : '';
                }
                return $arr;
            })->toArray();

        // Cash withdrawals that require approval: status 'pending' and type 'Withdrawal'
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

        $this->pendingTransactions = array_merge($pendingTransactions, $pendingCashWithdrawals);
    }

    public function approve($transactionId)
    {
        // Find the transaction in the pending list to determine its source
        $transaction = collect($this->pendingTransactions)->firstWhere('id', $transactionId);
        $source = $transaction['source'] ?? null;

        if ($source === 'transactions') {
            // Approve regular transaction (with discount)
            try {
                $this->approveTransactionUseCase->execute($transactionId, Auth::user()->id);
                session()->flash('message', 'Transaction approved and balances updated!');
                $this->loadPendingTransactions();
                $this->dispatch('$refresh');
                return;
            } catch (\Exception $e) {
                session()->flash('error', 'Failed to approve transaction: ' . $e->getMessage());
                return;
            }
        }

        // Try CashTransaction (for withdrawals only - deposits don't need approval)
        $cashTransaction = \App\Models\Domain\Entities\CashTransaction::find($transactionId);
        if ($cashTransaction && $cashTransaction->status === 'pending' && $cashTransaction->transaction_type === 'Withdrawal') {
            $safe = \App\Models\Domain\Entities\Safe::find($cashTransaction->safe_id);
            $amount = abs($cashTransaction->amount);

            // Branch withdrawal logic
            if ($cashTransaction->destination_branch_id && $cashTransaction->destination_safe_id) {
                // Deduct from selected branch's safe (destination_safe_id)
                $sourceSafe = \App\Models\Domain\Entities\Safe::find($cashTransaction->destination_safe_id);
                if ($sourceSafe) {
                    if (($sourceSafe->current_balance - $amount) < 0) {
                        session()->flash('error', 'Insufficient balance in source branch safe. Available: ' . number_format($sourceSafe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                        return;
                    }
                    $sourceSafe->current_balance -= $amount;
                    $sourceSafe->save();
                }
                // Add to agent's branch safe (safe_id)
                $destSafe = \App\Models\Domain\Entities\Safe::find($cashTransaction->safe_id);
                if ($destSafe) {
                    $destSafe->current_balance += $amount;
                    $destSafe->save();
                }
            } else {
                // Regular withdrawal logic
                // Check if this is a client wallet withdrawal (identified by the notes)
                if (str_contains($cashTransaction->notes, 'CLIENT_WALLET_WITHDRAWAL')) {
                    // For client wallet withdrawals, the customer balance was already deducted
                    // when the transaction was created, so no safe balance deduction needed
                    // Just update the status to completed
                } elseif ($cashTransaction->customer_code) {
                    // For non-client wallet withdrawals, deduct from client wallet if applicable
                    $client = \App\Models\Domain\Entities\Customer::where('customer_code', $cashTransaction->customer_code)->first();
                    if ($client) {
                        if (($client->balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient client balance. Available: ' . number_format($client->balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $client->balance -= $amount;
                        $client->save();
                    }

                    // Also deduct from safe for regular customer withdrawals
                    if ($safe) {
                        if (($safe->current_balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient safe balance. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $safe->current_balance -= $amount;
                        $safe->save();
                    }
                } elseif ($cashTransaction->destination_branch_id && !$cashTransaction->customer_code && str_contains($cashTransaction->customer_name, 'Expense:')) {
                    // Expense withdrawal
                    if ($safe) {
                        if (($safe->current_balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient safe balance for expense withdrawal. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $safe->current_balance -= $amount;
                        $safe->save();
                    }
                } else {
                    // Deduct from safe for other types of withdrawals (direct, user, admin)
                    if ($safe) {
                        if (($safe->current_balance - $amount) < 0) {
                            session()->flash('error', 'Insufficient safe balance. Available: ' . number_format($safe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
                            return;
                        }
                        $safe->current_balance -= $amount;
                        $safe->save();
                    }
                }
            }
            // Update status to completed
            $cashTransaction->status = 'completed';
            $cashTransaction->save();
            session()->flash('message', 'Cash withdrawal approved and balances updated!');
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
        $transaction = collect($this->pendingTransactions)->firstWhere('id', $transactionId);
        $source = $transaction['source'] ?? null;

        // Debug logging
        Log::debug('PendingComponent reject attempt', [
            'transaction_id' => $transactionId,
            'transaction_type' => $transaction['transaction_type'] ?? null,
            'transaction_status' => $transaction['status'] ?? null,
            'user_id' => $user->id,
            'user_roles' => $user->getRoleNames(),
            'source' => $source,
        ]);

        if (!$transaction) {
            session()->flash('error', 'Transaction not found.');
            return;
        }

        if ($source === 'cash_transactions') {
            // Handle cash withdrawal rejection
            $cashTransaction = \App\Models\Domain\Entities\CashTransaction::find($transactionId);
            if (!$cashTransaction) {
                session()->flash('error', 'Cash transaction not found.');
                return;
            }
            $isAdminOrSupervisor = $user->hasRole('admin') || $user->hasRole('general_supervisor');
            if ($isAdminOrSupervisor && $cashTransaction->status === 'pending' && $cashTransaction->transaction_type === 'Withdrawal') {
                $cashTransaction->status = 'rejected';
                $cashTransaction->rejected_by = $user->id;
                $cashTransaction->rejected_at = now();
                $cashTransaction->rejection_reason = 'Rejected by ' . $user->name;
                $cashTransaction->save();
                session()->flash('message', 'Cash withdrawal rejected successfully.');
                $this->loadPendingTransactions();
                $this->dispatch('$refresh');
                return;
            } else {
                session()->flash('error', 'You can only reject pending cash withdrawals as admin or supervisor.');
                return;
            }
        }

        // Default: handle as normal transaction
        $isAdminOrSupervisor = $user->hasRole('admin') || $user->hasRole('general_supervisor');
        $isCashTransaction = in_array($transaction['transaction_type'] ?? '', ['Withdrawal', 'Cash']);
        $isPending = ($transaction['status'] ?? '') === 'Pending';

        if ($user->can('approve-all-requests') || $user->can('approve-pending-transactions')) {
            // Admin/Auditor can reject any pending transaction or any cash transaction
            if (!($isPending || ($isAdminOrSupervisor && $isCashTransaction))) {
                session()->flash('error', 'You can only reject pending or cash transactions.');
                return;
            }
        } elseif ($user->can('approve-own-branch-transactions') && $user->branch_id === ($transaction['branch_id'] ?? null)) {
            // Branch Manager can reject pending transactions in their own branch
            if (!$isPending) {
                session()->flash('error', 'You can only reject pending transactions in your branch.');
                return;
            }
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
