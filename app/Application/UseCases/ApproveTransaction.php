<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Transaction;
use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Entities\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

class ApproveTransaction
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private SafeRepository $safeRepository,
        private LineRepository $lineRepository
    ) {}

    public function execute(int $transactionId, int $reviewerId): Transaction
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            throw new \Exception('Transaction not found.');
        }

        if ($transaction->status !== 'Pending') {
            throw new \Exception('Transaction is not pending and cannot be approved.');
        }

        $transaction->status = 'Completed';
        $transaction->approved_at = now();
        $transaction->approved_by = $reviewerId;

        $this->transactionRepository->save($transaction);

        if ($transaction->transaction_type === 'Safe Transfer' && $transaction->destination_safe_id) {
            $destinationSafe = $this->safeRepository->findById($transaction->destination_safe_id);
            if (!$destinationSafe) {
                throw new \Exception('Destination safe for transfer not found.');
            }
            $this->safeRepository->update(
                $destinationSafe->id,
                ['current_balance' => $destinationSafe->current_balance + $transaction->amount]
            );

            $admins = User::where('role', 'admin')->get();
            $sourceBranchUsers = User::where('branch_id', $transaction->safe->branch_id)
                                       ->whereIn('role', ['branch_manager', 'general_supervisor'])
                                       ->get();
            $sourceSafeName = $transaction->safe->name;
            $destinationSafeName = $destinationSafe->name;

            $message = "Cash transfer of " . $transaction->amount . " EGP from safe " . $sourceSafeName . " to safe " . $destinationSafeName . " has been approved and completed.";
            
            Notification::send($admins, new AdminNotification($message, route('transactions.edit', $transaction->id)));
            if ($sourceBranchUsers->count() > 0) {
                Notification::send($sourceBranchUsers, new AdminNotification($message, route('transactions.edit', $transaction->id)));
            }
        } elseif ($transaction->transaction_type === 'Withdrawal' && $transaction->line_id) {
            $line = $this->lineRepository->findById($transaction->line_id);
            if (!$line) {
                throw new \Exception('SIM Line not found for withdrawal transaction.');
            }

            // Deduct the amount from the SIM line's current balance
            $this->lineRepository->update(
                $line->id,
                ['current_balance' => $line->current_balance - $transaction->amount]
            );

            // Optionally, send notification for SIM withdrawal approval
            $admins = User::where('role', 'admin')->get();
            $agentUsers = User::where('id', $transaction->agent_id)->get();

            $message = "SIM withdrawal of " . $transaction->amount . " EGP from line " . $line->mobile_number . " has been approved.";
            
            Notification::send($admins, new AdminNotification($message, route('transactions.edit', $transaction->id)));
            if ($agentUsers->count() > 0) {
                Notification::send($agentUsers, new AdminNotification($message, route('transactions.edit', $transaction->id)));
            }
        }

        return $transaction;
    }
} 