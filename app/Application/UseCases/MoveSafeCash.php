<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Transaction;
use App\Models\Domain\Entities\Branch;
use App\Domain\Entities\User;
use App\Notifications\AdminNotification;
use Illuminate\Support\Facades\Notification;

class MoveSafeCash
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

    public function execute(string $fromSafeId, string $toSafeId, float $amount, int $agentId): Transaction
    {
        if ($amount <= 0) {
            throw new \InvalidArgumentException('Amount must be positive.');
        }

        $fromSafe = $this->safeRepository->findById($fromSafeId);
        $toSafe = $this->safeRepository->findById($toSafeId);

        if (!$fromSafe) {
            throw new \Exception('Source safe not found.');
        }

        if (!$toSafe) {
            throw new \Exception('Destination safe not found.');
        }

        if ($fromSafe->current_balance < $amount) {
            throw new \Exception('Insufficient balance in source safe. Available: ' . number_format($fromSafe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
        }

        // Validate balances before updating
        if (($fromSafe->current_balance - $amount) < 0) {
            throw new \Exception('Insufficient balance in source safe. Available: ' . number_format($fromSafe->current_balance, 2) . ' EGP, Required: ' . number_format($amount, 2) . ' EGP');
        }

        // Deduct from source safe
        $this->safeRepository->update($fromSafeId, ['current_balance' => $fromSafe->current_balance - $amount]);

        // Add to destination safe
        $this->safeRepository->update($toSafeId, ['current_balance' => $toSafe->current_balance + $amount]);

        // Record the transaction
        $transactionAttributes = [
            'customer_name' => 'Safe Transfer', // Generic name for internal transfer
            'customer_mobile_number' => 'N/A',
            // line_mobile_number removed as it doesn't exist in the transactions table
            'customer_code' => 'SAFE-TRF-' . uniqid(),
            'amount' => $amount,
            'commission' => 0.00,
            'deduction' => 0.00,
            'transaction_type' => 'Safe Transfer',
            'agent_id' => $agentId,
            'status' => 'Pending', // Set status to Pending for inter-safe transfers
            'transaction_date_time' => now(), // Set the current timestamp
            'branch_id' => $fromSafe->branch_id,
            'line_id' => null,
            'safe_id' => $fromSafeId,
            'destination_safe_id' => $toSafeId, // Store the destination safe ID
        ];

        $createdTransaction = $this->transactionRepository->create($transactionAttributes);

        // Notify Admin about cashbox transfer (it's now pending)
        $admins = User::role('admin')->get();
        $adminMessage = "A new pending cashbox transfer of " . $amount . " EGP from safe " . $fromSafe->name . " to safe " . $toSafe->name . " has been initiated by " . User::find($agentId)->name . ". Review required.";
        Notification::send($admins, new AdminNotification($adminMessage, route('transactions.edit', $createdTransaction->id, false)));

        // Notify receiving branch managers and general supervisors that a transfer is pending
        $receivingBranchUsers = User::where('branch_id', $toSafe->branch_id)
            ->role(['branch_manager', 'general_supervisor'])
            ->get();

        if ($receivingBranchUsers->count() > 0) {
            $receivingBranchMessage = "A pending cash transfer of " . $amount . " EGP from " . $fromSafe->name . " is awaiting your approval.";
            Notification::send($receivingBranchUsers, new AdminNotification($receivingBranchMessage, route('transactions.edit', $createdTransaction->id, false)));
        }

        return $createdTransaction;
    }
}
