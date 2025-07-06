<?php

namespace App\Application\UseCases;

use App\Models\Domain\Entities\Transaction;

class GetTransferHistory
{
    public function getRecipientsForUser($userId)
    {
        return Transaction::where('agent_id', $userId)
            ->orderByDesc('created_at')
            ->pluck('customer_name')
            ->unique()
            ->take(10)
            ->values()
            ->toArray();
    }
} 