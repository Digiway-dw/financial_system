<?php

namespace App\Policies;

use App\Constants\Roles;
use App\Models\Domain\Entities\Transaction;
use App\Domain\Entities\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TraineePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the trainee can view their dashboard.
     */
    public function viewTraineeDashboard(User $user): bool
    {
        return $user->hasRole(Roles::TRAINEE);
    }

    /**
     * Determine whether the trainee can create a transaction.
     * Trainees can create transactions, but they will be pending until approved
     */
    public function createTransaction(User $user): bool
    {
        return $user->hasRole(Roles::TRAINEE);
    }

    /**
     * Determine whether the trainee can view a specific transaction.
     */
    public function viewTransaction(User $user, Transaction $transaction): bool
    {
        // Trainees can only view transactions they personally created
        if ($user->hasRole(Roles::TRAINEE)) {
            return $transaction->agent_id === $user->id;
        }

        // Other roles like admin, supervisor, branch manager can view all transactions
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::AUDITOR);
    }

    /**
     * Determine whether the trainee can edit a transaction.
     */
    public function editTransaction(User $user, Transaction $transaction): bool
    {
        // Trainees can only edit pending transactions they personally created
        if ($user->hasRole(Roles::TRAINEE)) {
            return $transaction->agent_id === $user->id && $transaction->status === 'Pending';
        }

        return false;
    }

    /**
     * Determine whether the trainee can delete a transaction.
     */
    public function deleteTransaction(User $user, Transaction $transaction): bool
    {
        // Trainees cannot delete transactions
        return false;
    }

    /**
     * Determine whether the user can approve trainee transactions.
     */
    public function approveTraineeTransactions(User $user): bool
    {
        // Only admin, general supervisor, branch manager, and auditor can approve trainee transactions
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::AUDITOR);
    }

    // Add these methods to explicitly deny line update/delete for trainees
    public function updateLine(User $user, \App\Models\Domain\Entities\Line $line): bool
    {
        return false;
    }
    public function deleteLine(User $user, \App\Models\Domain\Entities\Line $line): bool
    {
        return false;
    }
}
