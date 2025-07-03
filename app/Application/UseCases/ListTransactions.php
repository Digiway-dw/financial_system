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

    public function execute(): array
    {
        $user = Auth::user();

        if ($user->can('view-all-branches-data')) {
            $result = $this->transactionRepository->filter([]);
            return $result['transactions'] ?? [];
        } elseif ($user->can('view-own-branch-data')) {
            $result = $this->transactionRepository->filter(['branch_id' => $user->branch_id]);
            return $result['transactions'] ?? [];
        }

        // Default for users with no specific view permissions, or if logic needs refinement
        return [];
    }
} 