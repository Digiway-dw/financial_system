<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Safe;
use App\Models\Domain\Entities\Transaction;

class MoveSafeCash
{
    private SafeRepository $safeRepository;
    private TransactionRepository $transactionRepository;

    public function __construct(SafeRepository $safeRepository, TransactionRepository $transactionRepository)
    {
        $this->safeRepository = $safeRepository;
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(string $fromSafeId, string $toSafeId, float $amount, string $agentName): Transaction
    {
        $fromSafe = $this->safeRepository->findById($fromSafeId);
        $toSafe = $this->safeRepository->findById($toSafeId);

        if (!$fromSafe) {
            throw new \Exception('Source safe not found.');
        }

        if (!$toSafe) {
            throw new \Exception('Destination safe not found.');
        }

        if ($fromSafe->balance < $amount) {
            throw new \Exception('Insufficient balance in source safe.');
        }

        // Deduct from source safe
        $this->safeRepository->update($fromSafeId, ['balance' => $fromSafe->balance - $amount]);

        // Add to destination safe
        $this->safeRepository->update($toSafeId, ['balance' => $toSafe->balance + $amount]);

        // Record the transaction
        $transactionAttributes = [
            'customer_name' => 'Safe Transfer', // Generic name for internal transfer
            'customer_mobile_number' => 'N/A',
            'line_mobile_number' => 'N/A',
            'customer_code' => 'SAFE-TRF-' . uniqid(),
            'amount' => $amount,
            'commission' => 0.00,
            'deduction' => 0.00,
            'transaction_type' => 'Safe Transfer',
            'agent_name' => $agentName, // Agent performing the transfer
            'status' => 'Completed', // Internal transfers are usually completed immediately
            'branch_id' => $fromSafe->branch_id, // Or the branch of the agent
            'line_id' => null, // No line involved
            'safe_id' => $fromSafeId, // Primary safe involved (source)
            // Potentially add a 'destination_safe_id' column to transactions table for clarity
        ];

        return $this->transactionRepository->create($transactionAttributes);
    }
} 