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
        $result = $this->transactionRepository->filter($filters);

        return [
            'transactions' => $result['transactions'] ?? [],
            'totals' => $result['totals'] ?? [
                'total_transferred' => 0,
                'total_commission' => 0,
                'total_deductions' => 0,
                'net_profit' => 0,
            ],
        ];
    }
} 