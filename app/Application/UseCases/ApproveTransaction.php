<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Transaction;
use App\Domain\Interfaces\TransactionRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\LineRepository;
use App\Domain\Entities\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;
use App\Models\Domain\Entities\Safe;

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
        if (strtolower($transaction->status) !== 'pending') {
            throw new \Exception('Transaction is not pending and cannot be approved.');
        }

        $transaction->status = 'completed';
        $transaction->approved_at = now();
        $transaction->approved_by = $reviewerId;

        $this->transactionRepository->save($transaction);

        // Apply balance changes for all transaction types on approval
        if (in_array($transaction->transaction_type, ['Transfer', 'Withdrawal', 'Deposit', 'Receive'])) {
            $line = $transaction->line_id ? $this->lineRepository->findById($transaction->line_id) : null;
            $safe = $transaction->safe_id ? $this->safeRepository->findById($transaction->safe_id) : null;
            $amount = $transaction->amount;
            $commission = $transaction->commission ?? 0;
            $deduction = $transaction->deduction ?? 0;
            $paymentMethod = $transaction->payment_method ?? null;

            if ($transaction->transaction_type === 'Transfer') {
                if ($line) {
                    // Add 1 EGP fee for send transactions
                    $lineDeduction = $amount + 1;
                    $this->lineRepository->update($line->id, [
                        'current_balance' => $line->current_balance - $lineDeduction
                    ]);
                }
            } elseif ($transaction->transaction_type === 'Withdrawal') {
                if ($safe) {
                    $this->safeRepository->update($safe->id, [
                        'current_balance' => $safe->current_balance - $amount
                    ]);
                }
                if ($line) {
                    $this->lineRepository->update($line->id, [
                        'current_balance' => $line->current_balance - $amount
                    ]);
                }
            } elseif ($transaction->transaction_type === 'Deposit' || $transaction->transaction_type === 'Receive') {
                if ($line) {
                    $this->lineRepository->update($line->id, [
                        'current_balance' => $line->current_balance + $amount,
                        'daily_usage' => ($line->daily_usage ?? 0) + $amount,
                        'monthly_usage' => ($line->monthly_usage ?? 0) + $amount,
                    ]);
                }
                if ($safe) {
                    $this->safeRepository->update($safe->id, [
                        'current_balance' => $safe->current_balance - ($amount - $commission)
                    ]);
                }
            }
        }

        if ($transaction->transaction_type === 'line_transfer') {
            // Handle line transfer approval (NO NOTIFICATIONS)
            $fromLine = $this->lineRepository->findById($transaction->from_line_id);
            $toLine = $this->lineRepository->findById($transaction->to_line_id);
          
            if (!$fromLine || !$toLine) {
              
                throw new \Exception('Source or destination line not found for line transfer.');
            }
            // Check if source line still has sufficient balance
            if ($fromLine->current_balance < $transaction->total_deducted) {
               
                throw new \Exception(
                    'Insufficient balance in source line. Available: ' . 
                    number_format($fromLine->current_balance, 2) . 
                    ' EGP, Required: ' . number_format($transaction->total_deducted, 2) . ' EGP'
                );
            }
            // Apply balance changes
            $this->lineRepository->update($fromLine->id, [
                'current_balance' => $fromLine->current_balance - $transaction->total_deducted
            ]);
            $this->lineRepository->update($toLine->id, [
                'current_balance' => $toLine->current_balance + $transaction->amount
            ]);
          
            // Do NOT send notifications for line transfer approval
        }

        if ($transaction->transaction_type === 'Safe Transfer' && $transaction->destination_safe_id) {
            $destinationSafe = $this->safeRepository->findById($transaction->destination_safe_id);
            if (!$destinationSafe) {
                throw new \Exception('Destination safe for transfer not found.');
            }
            $this->safeRepository->update(
                $destinationSafe->id,
                ['current_balance' => $destinationSafe->current_balance + $transaction->amount]
            );

            $admins = User::role('admin')->get();
            $sourceBranchUsers = User::where('branch_id', $transaction->safe->branch_id)
                                       ->whereIn('role', ['branch_manager', 'general_supervisor'])
                                       ->get();
            $sourceSafeName = $transaction->safe->name;
            $destinationSafeName = $destinationSafe->name;

            $message = "Cash transfer of " . $transaction->amount . " EGP from safe " . $sourceSafeName . " to safe " . $destinationSafeName . " has been approved and completed.";
            
            Notification::send($admins, new AdminNotification($message, route('transactions.edit', $transaction->reference_number, false)));
            if ($sourceBranchUsers->count() > 0) {
                Notification::send($sourceBranchUsers, new AdminNotification($message, route('transactions.edit', $transaction->reference_number, false)));
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
            $agentUsers = User::where('id', $transaction->agent_id)->get();

            $message = "SIM withdrawal of " . $transaction->amount . " EGP from line " . $line->mobile_number . " has been approved.";
            
            Notification::send($agentUsers, new AdminNotification($message, route('transactions.edit', $transaction->reference_number, false)));
        }

        // Only send notifications for non-line_transfer transactions
        if ($transaction->transaction_type !== 'line_transfer') {
            $admins = User::role('admin')->get();
            $adminMessage = "Transaction " . $transaction->customer_name . " with amount " . $transaction->amount . " EGP has been approved by " . User::find($reviewerId)->name . ".";
            Notification::send($admins, new AdminNotification($adminMessage, route('transactions.edit', $transaction->reference_number, false)));

            // Additional notification for admins on transactions from safe type 'cashbox'
            if ($transaction->safe_id) {
                $safe = Safe::find($transaction->safe_id);
                if ($safe && $safe->type === 'cashbox') {
                    $admins = User::role('admin')->get();
                    $adminNotificationMessage = "A transaction of " . $transaction->amount . " EGP from cashbox safe: " . $safe->name . " has been approved by " . User::find($reviewerId)->name . ".";
                    Notification::send($admins, new AdminNotification($adminNotificationMessage, route('transactions.edit', $transaction->reference_number, false)));
                }
            }
        }

        return $transaction;
    }
}