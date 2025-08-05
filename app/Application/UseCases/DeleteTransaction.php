<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Auth\Access\AuthorizationException;

class DeleteTransaction
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    public function execute(string $transactionId): bool
    {
        $transaction = $this->transactionRepository->findById($transactionId);

        if (!$transaction) {
            return false;
        }

        // Check if user is authorized to delete transactions (same as view authorization)
        if (!Gate::allows('delete-transactions')) {
            throw new AuthorizationException('You are not authorized to delete transactions.');
        }

        $this->transactionRepository->delete($transactionId);

        return true;
    }
}
