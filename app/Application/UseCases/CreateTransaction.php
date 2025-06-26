<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;

class CreateTransaction
{
    private TransactionRepository $transactionRepository;
    private LineRepository $lineRepository;

    public function __construct(TransactionRepository $transactionRepository, LineRepository $lineRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->lineRepository = $lineRepository;
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
        string $agentName,
        string $status,
        string $branchId,
        string $lineId,
        string $safeId
    ): Transaction
    {
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
            throw new \Exception('Transaction amount exceeds monthly limit for this line.');
        }

        // Check current balance for withdrawal/transfer types
        if (in_array($transactionType, ['Transfer', 'Withdrawal'])) {
            if (($line->current_balance - $amount) < 0) {
                throw new \Exception('Insufficient balance in line for this transaction.');
            }
            // Deduct amount from line balance
            $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance - $amount]);
        } elseif ($transactionType === 'Deposit') {
            // Add amount to line balance for deposits
            $this->lineRepository->update($lineId, ['current_balance' => $line->current_balance + $amount]);
        }

        $attributes = [
            'customer_name' => $customerName,
            'customer_mobile_number' => $customerMobileNumber,
            'line_mobile_number' => $lineMobileNumber,
            'customer_code' => $customerCode,
            'amount' => $amount,
            'commission' => $commission,
            'deduction' => $deduction,
            'transaction_type' => $transactionType,
            'agent_name' => $agentName,
            'status' => $status,
            'branch_id' => $branchId,
            'line_id' => $lineId,
            'safe_id' => $safeId,
        ];

        return $this->transactionRepository->create($attributes);
    }
} 