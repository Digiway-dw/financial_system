<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;
use App\Notifications\AdminNotification;
use App\Notifications\SafeTransferNotification;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use App\Constants\Roles;

class CreateSafeToSafeTransfer
{
    private SafeRepository $safeRepository;
    private TransactionRepository $transactionRepository;

    public function __construct(
        SafeRepository $safeRepository,
        TransactionRepository $transactionRepository
    ) {
        $this->safeRepository = $safeRepository;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * Create a new safe-to-safe transfer transaction
     * 
     * @param int $sourceSafeId The ID of the source safe
     * @param int $destinationSafeId The ID of the destination safe
     * @param float $amount The amount to transfer
     * @param int $initiatorId The ID of the user initiating the transfer
     * @param string $notes Any notes about the transfer
     * @return Transaction The created transaction
     */
    public function execute(
        int $sourceSafeId,
        int $destinationSafeId,
        float $amount,
        int $initiatorId,
        string $notes = ''
    ): Transaction {
        // Get the safes
        $sourceSafe = $this->safeRepository->findById($sourceSafeId);
        $destinationSafe = $this->safeRepository->findById($destinationSafeId);

        if (!$sourceSafe || !$destinationSafe) {
            throw new \Exception('Source or destination safe not found.');
        }

        // Get the initiator
        $initiator = \App\Domain\Entities\User::find($initiatorId);
        if (!$initiator) {
            throw new \Exception('Initiator not found.');
        }

        // Check if amount is valid
        if ($amount <= 0) {
            throw new \Exception('Transfer amount must be greater than zero.');
        }

        // Check if source safe has enough balance
        if ($sourceSafe->current_balance < $amount) {
            throw new \Exception('Insufficient balance in source safe. Available: ' . number_format($sourceSafe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
        }

        // Determine status - Only Admin or General Supervisor can do unrestricted transfers, others need approval
        $requiresApproval = !($initiator->hasRole(Roles::ADMIN) || $initiator->hasRole(Roles::GENERAL_SUPERVISOR));
        $status = $requiresApproval ? 'Pending' : 'Completed';

        // Generate unique reference number
        $referenceNumber = $this->generateUniqueReferenceNumber($initiator);

        // Create the transaction
        $transaction = new Transaction([
            'transaction_type' => 'Safe Transfer',
            'amount' => $amount,
            'commission' => 0, // No commission on safe-to-safe transfers
            'deduction' => 0, // No deduction on safe-to-safe transfers
            'agent_id' => $initiatorId,
            'safe_id' => $sourceSafeId,
            'destination_safe_id' => $destinationSafeId,
            'status' => $status,
            'notes' => $notes,
            'reference_number' => $referenceNumber,
            'transaction_date_time' => now(),
        ]);

        $createdTransaction = $this->transactionRepository->save($transaction);

        // If Admin or General Supervisor (unrestricted), update balances immediately
        if (!$requiresApproval) {
            // Validate balances before updating
            if (($sourceSafe->current_balance - $amount) < 0) {
                throw new \Exception('Insufficient balance in source safe. Available: ' . number_format($sourceSafe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
            }
            
            // Deduct from source safe
            $this->safeRepository->update(
                $sourceSafeId,
                ['current_balance' => $sourceSafe->current_balance - $amount]
            );

            // Add to destination safe
            $this->safeRepository->update(
                $destinationSafeId,
                ['current_balance' => $destinationSafe->current_balance + $amount]
            );

            // Notify relevant users about completed transfer
            $this->notifyAboutCompletedTransfer($createdTransaction, $sourceSafe, $destinationSafe, $initiator);
        } else {
            // For non-Admin, validate balance before deducting
            if (($sourceSafe->current_balance - $amount) < 0) {
                throw new \Exception('Insufficient balance in source safe. Available: ' . number_format($sourceSafe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
            }
            
            // Deduct from source safe immediately but recipient needs to approve
            $this->safeRepository->update(
                $sourceSafeId,
                ['current_balance' => $sourceSafe->current_balance - $amount]
            );

            // Notify approvers about pending transfer
            $this->notifyAboutPendingTransfer($createdTransaction, $sourceSafe, $destinationSafe, $initiator);
        }

        return $createdTransaction;
    }

    /**
     * Generate a unique reference number for the transaction
     */
    private function generateUniqueReferenceNumber($initiator): string
    {
        // Get the branch code from the initiator's branch
        $branchCode = $initiator->branch ? $initiator->branch->branch_code : 'DEFAULT';

        // Generate date part (YYYYMMDD)
        $datePart = date('Ymd');

        // Find the highest existing sequence number for today and this branch
        $pattern = $branchCode . '-' . $datePart . '-%';
        $lastTransaction = Transaction::where('reference_number', 'like', $pattern)
            ->orderBy('reference_number', 'desc')
            ->first();

        $nextSequence = 1;
        if ($lastTransaction) {
            // Extract the sequence number from the last reference number
            $lastSequence = intval(substr($lastTransaction->reference_number, -6));
            $nextSequence = $lastSequence + 1;
        }

        // Generate the reference number: BRANCHCODE-YYYYMMDD-XXXXXX
        $referenceNumber = $branchCode . '-' . $datePart . '-' . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);

        // Double-check uniqueness (in case of race conditions)
        $attempts = 0;
        while (Transaction::where('reference_number', $referenceNumber)->exists() && $attempts < 10) {
            $nextSequence++;
            $referenceNumber = $branchCode . '-' . $datePart . '-' . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);
            $attempts++;
        }

        if ($attempts >= 10) {
            throw new \Exception('Unable to generate unique reference number after 10 attempts');
        }

        return $referenceNumber;
    }

    /**
     * Notify relevant users about a completed transfer
     */
    private function notifyAboutCompletedTransfer($transaction, $sourceSafe, $destinationSafe, $initiator): void
    {
        // Notify admins and general supervisors
        $admins = \App\Domain\Entities\User::role(Roles::ADMIN)->get();
        $supervisors = \App\Domain\Entities\User::role(Roles::GENERAL_SUPERVISOR)->get();

        // Get branch managers of source and destination branches
        $sourceBranchManagers = \App\Domain\Entities\User::role(Roles::BRANCH_MANAGER)
            ->where('branch_id', $sourceSafe->branch_id)
            ->get();

        $destinationBranchManagers = \App\Domain\Entities\User::role(Roles::BRANCH_MANAGER)
            ->where('branch_id', $destinationSafe->branch_id)
            ->get();

        // Combine all recipients
        $recipients = $admins->merge($supervisors)
            ->merge($sourceBranchManagers)
            ->merge($destinationBranchManagers)
            ->unique();

        $message = "Safe-to-safe transfer of " . number_format($transaction->amount, 2) . " EGP from " .
            $sourceSafe->name . " to " . $destinationSafe->name .
            " has been completed by " . $initiator->name . ".";

        Notification::send($recipients, new AdminNotification(
            $message,
            route('transactions.details', $transaction->reference_number)
        ));
    }

    /**
     * Notify relevant users about a pending transfer that needs approval
     */
    private function notifyAboutPendingTransfer($transaction, $sourceSafe, $destinationSafe, $initiator): void
    {
        // Notify admins, general supervisors, and branch managers who can approve
        $approvers = \App\Domain\Entities\User::where(function ($query) {
            $query->role(Roles::ADMIN)
                ->orWhere(function ($q) {
                    $q->role(Roles::GENERAL_SUPERVISOR);
                });
        })
            ->orWhere(function ($query) use ($destinationSafe) {
                $query->role(Roles::BRANCH_MANAGER)
                    ->where('branch_id', $destinationSafe->branch_id);
            })
            ->get();

        $message = "Safe-to-safe transfer of " . number_format($transaction->amount, 2) . " EGP from " .
            $sourceSafe->name . " to " . $destinationSafe->name .
            " initiated by " . $initiator->name . " requires your approval.";

        Notification::send($approvers, new AdminNotification(
            $message,
            route('transactions.pending')
        ));
    }
}
