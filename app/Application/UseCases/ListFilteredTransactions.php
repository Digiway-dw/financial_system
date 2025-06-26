<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;

class ListFilteredTransactions
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(array $filters = []): array
    {
        return $this->transactionRepository->filter($filters);
    }
} 