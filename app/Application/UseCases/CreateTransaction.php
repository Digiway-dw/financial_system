<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;

class CreateTransaction
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
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