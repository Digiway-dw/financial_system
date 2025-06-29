<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

class CreateTransaction
{
    private TransactionRepository $transactionRepository;
    private LineRepository $lineRepository;
    private SafeRepository $safeRepository;

    public function __construct(TransactionRepository $transactionRepository, LineRepository $lineRepository, SafeRepository $safeRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->lineRepository = $lineRepository;
        $this->safeRepository = $safeRepository;
    }

    public function execute(
        string $customerName,
        string $customerMobileNumber,
        string $lineMobileNumber,
        ?string $customerCode,
        float $amount,
        float $commission,
        float $deduction,
        string $transactionType,
        int $agentId,
        string $branchId,
        string $lineId,
        string $safeId,
        bool $isAbsoluteWithdrawal = false
    ): Transaction
    {
        // Validate amount: integer only, multiples of 5
        if (!is_int($amount) && !($amount == (int)$amount)) {
            throw new \InvalidArgumentException('Amount must be an integer.');
        }
        if ($amount % 5 !== 0) {
            throw new \InvalidArgumentException('Amount must be a multiple of 5.');
        }

        // Fetch the agent to check role for status setting
        $agent = \App\Domain\Entities\User::find($agentId);
        if (!$agent) {
            throw new \Exception('Agent not found.');
        }

        // Auto-calculate commission (5 EGP per 500 EGP)
        $calculatedCommission = (floor($amount / 500)) * 5;

        // Apply optional deduction to commission
        $finalCommission = $calculatedCommission - $deduction;
        if ($finalCommission < 0) {
            $finalCommission = 0; // Commission cannot be negative
        }

        // Determine transaction status based on agent role and if it's an absolute withdrawal
        // Absolute withdrawals by Admin do not require approval and are 'Completed'
        if ($agent->isAdmin() && $isAbsoluteWithdrawal) {
            $status = 'Completed';
        } else {
            // If agent has a deduction, mark as pending, otherwise, mark as completed unless Trainee
            if ($agent->isAgent() && $deduction > 0) {
                $status = 'Pending';
            } else {
                $status = $agent->isTrainee() ? 'Pending' : 'Completed';
            }
        }

        // Additional check for withdrawals from Client Safes by non-admin/supervisor
        $safe = $this->safeRepository->findById($safeId); // Fetch safe here to check type
        if (!$safe) {
            throw new \Exception('Safe not found.');
        }

        if ($transactionType === 'Withdrawal' && $safe->isClientSafe()) {
            if (!($agent->isAdmin() || $agent->isGeneralSupervisor())) {
                $status = 'Pending'; // Override status to Pending for client safe withdrawals needing approval
                $notificationMessage = "A withdrawal of " . $amount . " EGP from Client Safe '" . $safe->name . "' by " . $agent->name . " requires your approval.";
                $this->notifyRelevantUsers($notificationMessage, route('transactions.edit', $createdTransaction->id ?? null), null); // Branch ID null for admin/supervisor notification
            }
        }

        // Fetch the line to check limits and balance
        $line = $this->lineRepository->findById($lineId);

        if (!$line) {
            throw new \Exception('Line not found.');
        }

        // Check daily limit
        // This would ideally involve checking aggregated daily transactions for this line.
        // For simplicity, let's assume the daily limit is checked against the current transaction amount directly.
        if ($amount > $line->daily_limit) {
            throw new \Exception('Transaction amount exceeds daily limit for this line.');
        }

        // Check monthly limit
        // Similar to daily limit, this would require aggregation.
        if ($amount > $line->monthly_limit) { // This is a simplistic check
            // Notify admin if monthly threshold is crossed
            $admins = \App\Domain\Entities\User::where('role', 'admin')->get();
            $message = "Monthly limit of line " . $line->mobile_number . " (assigned to " . $line->user->name . ") has been crossed.";
            Notification::send($admins, new AdminNotification($message, route('lines.edit', $line->id)));
            throw new \Exception('Transaction amount exceeds monthly limit for this line.');
        }

        // Check current balance for withdrawal/transfer types
        if (in_array($transactionType, ['Transfer', 'Withdrawal'])) {
            if (($line->current_balance - $amount) < 0) {
                throw new \Exception('Insufficient balance in line for this transaction.');
            }
            // Deduct amount from line balance
            $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance - $amount]);
            $line->refresh(); // Refresh to get the updated balance

            // Check for low line balance after deduction
            if ($line->current_balance < 500) { // Example threshold: 500 EGP
                $notificationMessage = "Warning: Line " . $line->mobile_number . " balance is low ( " . $line->current_balance . " EGP). Please top up.";
                $this->notifyRelevantUsers($notificationMessage, route('lines.edit', $line->id), $line->user->branch_id);
            }

            // Only deduct from safe if it's not an absolute withdrawal
            if (!$isAbsoluteWithdrawal) {
                $safe = $this->safeRepository->findById($safeId);
                if (!$safe) {
                    throw new \Exception('Safe not found.');
                }

                if (($safe->current_balance - $amount) < 0) {
                    throw new \Exception('Insufficient balance in safe for this transaction.');
                }
                $this->safeRepository->update($safeId, ['current_balance' => $safe->current_balance - $amount]);
                $safe->refresh(); // Refresh to get the updated balance

                // Check for low safe balance after deduction
                if ($safe->current_balance < 500) { // Example threshold: 500 EGP
                    $notificationMessage = "Warning: Safe " . $safe->name . " balance is low ( " . $safe->current_balance . " EGP) in branch " . $safe->branch->name . ". Please deposit.";
                    $this->notifyRelevantUsers($notificationMessage, route('safes.edit', $safe->id), $safe->branch_id);
                }
            }

        } elseif ($transactionType === 'Deposit') {
            // Add amount to line balance for deposits
            $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance + $amount]);
            $line->refresh(); // Refresh to get the updated balance

            // Check for low line balance after deposit (in case it was negative and just got positive, but still low)
            if ($line->current_balance < 500) {
                $notificationMessage = "Warning: Line " . $line->mobile_number . " balance is low ( " . $line->current_balance . " EGP). Please top up.";
                $this->notifyRelevantUsers($notificationMessage, route('lines.edit', $line->id), $line->user->branch_id);
            }

            // Add amount to safe balance for deposits
            $safe = $this->safeRepository->findById($safeId);
            if (!$safe) {
                throw new \Exception('Safe not found.');
            }
            $this->safeRepository->update($safeId, ['current_balance' => $safe->current_balance + $amount]);
            $safe->refresh(); // Refresh to get the updated balance

            // Check for low safe balance after deposit
            if ($safe->current_balance < 500) {
                $notificationMessage = "Warning: Safe " . $safe->name . " balance is low ( " . $safe->current_balance . " EGP) in branch " . $safe->branch->name . ". Please deposit.";
                $this->notifyRelevantUsers($notificationMessage, route('safes.edit', $safe->id), $safe->branch_id);
            }
        }

        $attributes = [
            'customer_name' => $customerName,
            'customer_mobile_number' => $customerMobileNumber,
            'line_mobile_number' => $lineMobileNumber,
            'customer_code' => $customerCode,
            'amount' => $amount,
            'commission' => $finalCommission,
            'deduction' => $deduction,
            'transaction_type' => $transactionType,
            'agent_id' => $agentId,
            'status' => $status,
            'transaction_date_time' => now(),
            'branch_id' => $branchId,
            'line_id' => $lineId,
            'safe_id' => $safeId,
            'is_absolute_withdrawal' => $isAbsoluteWithdrawal,
        ];

        $createdTransaction = $this->transactionRepository->create($attributes);

        // Send notifications to Admin for deductions or pending transactions
        $admins = \App\Domain\Entities\User::where('role', 'admin')->get();

        if ($deduction > 0) {
            $message = "A new transaction with a deduction of " . $deduction . " EGP has been created by " . $agent->name . ".";
            Notification::send($admins, new AdminNotification($message, route('transactions.edit', $createdTransaction->id)));
        }

        if ($status === 'Pending') {
            $message = "A new pending transaction has been created by " . $agent->name . ". Review required.";
            // Avoid sending duplicate notifications if already sent for deduction or client safe withdrawal
            if (!($deduction > 0 && $agent->isAgent()) && !($transactionType === 'Withdrawal' && $safe->isClientSafe() && !($agent->isAdmin() || $agent->isGeneralSupervisor()))) { 
                Notification::send($admins, new AdminNotification($message, route('transactions.edit', $createdTransaction->id)));
            }
        }

        return $createdTransaction;
    }

    // Helper function to notify relevant users
    private function notifyRelevantUsers(string $message, string $url, ?int $branchId = null): void
    {
        $recipients = \App\Domain\Entities\User::where('role', 'admin')
                                               ->orWhere('role', 'general_supervisor');

        if ($branchId) {
            $recipients->orWhere(function ($query) use ($branchId) {
                $query->where('branch_id', $branchId)
                      ->whereIn('role', ['branch_manager']);
            });
        }

        Notification::send($recipients->get(), new AdminNotification($message, $url));
    }
} 