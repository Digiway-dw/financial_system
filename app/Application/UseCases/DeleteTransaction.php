<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;

class DeleteTransaction
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(string $transactionId): bool
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            return false;
        }

        $this->transactionRepository->delete($transactionId);

        return true;
    }
} 