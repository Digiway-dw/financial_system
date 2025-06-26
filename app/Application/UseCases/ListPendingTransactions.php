<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;

class ListPendingTransactions
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(): array
    {
        // Assuming 'status' is a column in your transactions table
        // and 'Pending' is the status for unapproved transactions.
        return array_filter($this->transactionRepository->all(), function ($transaction) {
            return $transaction['status'] === 'Pending';
        });
    }
} 