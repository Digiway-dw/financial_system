<?php

namespace App\Infrastructure\Repositories;

use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Transaction as EloquentTransaction;
use App\Models\Domain\Entities\Transaction;

class EloquentTransactionRepository implements TransactionRepository
{
    public function create(array $attributes): Transaction
    {
        return EloquentTransaction::create($attributes);
    }

    public function findById(string $id): ?Transaction
    {
        return EloquentTransaction::find($id);
    }

    public function update(string $id, array $attributes): Transaction
    {
        $transaction = EloquentTransaction::findOrFail($id);
        $transaction->update($attributes);
        return $transaction;
    }

    public function delete(string $id): void
    {
        EloquentTransaction::destroy($id);
    }

    public function all(): array
    {
        return EloquentTransaction::all()->toArray();
    }

    public function filter(array $filters = []): array
    {
        $query = EloquentTransaction::query();

        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }

        if (isset($filters['user_id'])) {
            // Assuming a relationship or agent_name field
            // For now, let's filter by agent_name if user_id implies agent
            // Or we need to load the user relationship and filter by user ID
            // Let's use agent_name for simplicity first, then refine if needed
            $query->where('agent_name', $filters['user_id']); // This needs to be refined based on how user_id maps to agent_name
        }

        if (isset($filters['branch_id'])) {
            $query->where('branch_id', $filters['branch_id']);
        }

        if (isset($filters['customer_id'])) {
            // Assuming we will store customer ID or code in transaction
            $query->where('customer_name', 'like', '%' . $filters['customer_id'] . '%'); // This needs to be refined, maybe using customer_code
        }

        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        return $query->get()->toArray();
    }
} 