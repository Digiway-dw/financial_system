<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;

class ApproveTransaction
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(string $transactionId): Transaction
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            throw new \Exception('Transaction not found.');
        }

        // Update the status to 'Completed'
        return $this->transactionRepository->update($transactionId, ['status' => 'Completed']);
    }
} 