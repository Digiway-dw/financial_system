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

        // Convert to Transaction model instance to use policies
        $transactionModel = Transaction::find($transactionId);

        // Check if user is authorized to delete this transaction
        if (!Gate::allows('deleteTransaction', $transactionModel)) {
            throw new AuthorizationException('You are not authorized to delete this transaction.');
        }

        $this->transactionRepository->delete($transactionId);

        return true;
    }
}
