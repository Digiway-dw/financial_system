<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;

class ListTransactions
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(): array
    {
        return $this->transactionRepository->all();
    }
} 