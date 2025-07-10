<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\TransactionRepository;
use Illuminate\Support\Facades\Auth;
use App\Constants\Roles;
use Illuminate\Support\Facades\Gate;
use App\Domain\Entities\User;

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

        if (!$user) {
            return [];
        }

        // Approach: Apply filters based on role permissions using Gates

        // Admin, General Supervisor, and Auditor can see all transactions across branches
        if (Gate::forUser($user)->allows('view-all-branches-data')) {
            $result = $this->transactionRepository->filter($filters);
            return $result['transactions'] ?? [];
        }

        // Branch Managers can only see transactions from their branch
        elseif (Gate::forUser($user)->allows('view-own-branch-data')) {
            $filters['branch_id'] = $user->branch_id;
            $result = $this->transactionRepository->filter($filters);
            return $result['transactions'] ?? [];
        }

        // Agents can only see their own transactions
        elseif (Gate::forUser($user)->allows('view-agent-dashboard')) {
            $filters['agent_id'] = $user->id;
            $result = $this->transactionRepository->filter($filters);
            return $result['transactions'] ?? [];
        }

        // Trainees can only see their own transactions (similar to agents)
        elseif (Gate::forUser($user)->allows('view-trainee-dashboard')) {
            $filters['agent_id'] = $user->id;
            $result = $this->transactionRepository->filter($filters);
            return $result['transactions'] ?? [];
        }

        // Default for users with no specific view permissions
        return [];
    }
}
