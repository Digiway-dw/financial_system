<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use Illuminate\Support\Facades\Auth;

class ListPendingTransactions
{
    public function __construct(
        private TransactionRepository $transactionRepository
    ) {}

    public function execute(?int $branchId = null): array
    {
    // Use 'pending' (lowercase) as the status for pending transactions
    return $this->transactionRepository->findByStatus('pending', $branchId);
    }

    public function findById(string $transactionId): ?array
    {
        $transaction = $this->transactionRepository->findById($transactionId);
        return $transaction ? $transaction->toArray() : null;
    }
} 