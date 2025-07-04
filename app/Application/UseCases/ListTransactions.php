<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use Illuminate\Support\Facades\Auth;

class ListTransactions
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(array $filters = []): array
    {
        $user = Auth::user();

        if ($user->can('view-all-branches-data')) {
            $result = $this->transactionRepository->filter($filters);
            return $result['transactions'] ?? [];
        } elseif ($user->can('view-own-branch-data')) {
            $filters['branch_id'] = $user->branch_id;
            $result = $this->transactionRepository->filter($filters);
            return $result['transactions'] ?? [];
        }

        // Default for users with no specific view permissions, or if logic needs refinement
        return [];
    }
} 