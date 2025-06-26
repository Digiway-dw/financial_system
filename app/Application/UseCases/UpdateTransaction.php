<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;

class UpdateTransaction
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(string $id, array $attributes): Transaction
    {
        $transaction = $this->transactionRepository->findById($id);

        if (!$transaction) {
            throw new \Exception('Transaction not found.');
        }

        return $this->transactionRepository->update($id, $attributes);
    }
} 