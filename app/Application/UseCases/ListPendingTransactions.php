<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;

class ListPendingTransactions
{
    public function __construct(
        private TransactionRepository $transactionRepository
    ) {}

    public function execute(): array
    {
        // Assuming 'Pending' is the status for pending transactions
        return $this->transactionRepository->findByStatus('Pending');
    }
} 