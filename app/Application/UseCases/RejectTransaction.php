<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Transaction;
use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Entities\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

class RejectTransaction
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private SafeRepository $safeRepository
    ) {}

    public function execute(int $transactionId, int $reviewerId, ?string $rejectionReason = null): Transaction
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            throw new \Exception('Transaction not found.');
        }

        if ($transaction->status !== 'Pending') {
            throw new \Exception('Transaction is not pending and cannot be rejected.');
        }

        $transaction->status = 'Rejected';
        $transaction->rejected_at = now();
        $transaction->rejected_by = $reviewerId;
        $transaction->rejection_reason = $rejectionReason;

        $this->transactionRepository->save($transaction);

        if ($transaction->transaction_type === 'Safe Transfer' && $transaction->safe_id) {
            $sourceSafe = $this->safeRepository->findById($transaction->safe_id);
            if (!$sourceSafe) {
                throw new \Exception('Source safe for rejected transfer not found.');
            }
            $this->safeRepository->update(
                $sourceSafe->id,
                ['current_balance' => $sourceSafe->current_balance + $transaction->amount]
            );

            $admins = User::role('admin')->get();
            $sourceBranchUsers = User::where('branch_id', $sourceSafe->branch_id)
                                       ->whereIn('role', ['branch_manager', 'general_supervisor'])
                                       ->get();
            $sourceSafeName = $sourceSafe->name;
            $destinationSafeName = $transaction->destinationSafe ? $transaction->destinationSafe->name : 'N/A';

            $message = "Cash transfer of " . $transaction->amount . " EGP from safe " . $sourceSafeName . " to safe " . $destinationSafeName . " has been rejected. Amount re-credited to source safe.";
            
            Notification::send($admins, new AdminNotification($message, route('transactions.edit', $transaction->id, false)));
            if ($sourceBranchUsers->count() > 0) {
                Notification::send($sourceBranchUsers, new AdminNotification($message, route('transactions.edit', $transaction->id, false)));
            }
        }

        $agent = User::find($reviewerId);
        if ($agent) {
            $agentMessage = "Transaction " . $transaction->customer_name . " with amount " . $transaction->amount . " EGP has been rejected by " . $agent->name . ". Reason: " . $rejectionReason . ".";
            Notification::send($agent, new AdminNotification($agentMessage, route('transactions.edit', $transaction->id, false)));
        }

        $admins = User::role('admin')->get();
        $adminMessage = "Transaction " . $transaction->customer_name . " with amount " . $transaction->amount . " EGP has been rejected by " . User::find($reviewerId)->name . ". Reason: " . $rejectionReason . ".";
        Notification::send($admins, new AdminNotification($adminMessage, route('transactions.edit', $transaction->id, false)));

        return $transaction;
    }
} 