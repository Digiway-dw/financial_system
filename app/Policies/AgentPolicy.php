<?php

namespace App\Policies;

use App\Constants\Roles;
use App\Models\Domain\Entities\Transaction;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Line;
use Illuminate\Auth\Access\HandlesAuthorization;

class AgentPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the agent can view their dashboard.
     */
    public function viewAgentDashboard(User $user): bool
    {
        return $user->hasRole(Roles::AGENT);
    }

    /**
     * Determine whether the agent can view a specific transaction.
     */
    public function viewTransaction(User $user, Transaction $transaction): bool
    {
        // Agents can only view transactions they personally created
        if ($user->hasRole(Roles::AGENT)) {
            return $transaction->agent_id === $user->id;
        }

        // Other roles like admin, supervisor, branch manager can view all transactions
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can create a new transaction.
     */
    public function createTransaction(User $user): bool
    {
        // All agents can create transactions
        return true;
    }

    /**
     * Determine whether the agent can view a line's balance.
     */
    public function viewLineBalance(User $user, Line $line): bool
    {
        // Agents can no longer view lines by user_id, as lines are not assigned to users
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }
        // Other roles can view all lines
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can edit a transaction.
     */
    public function editTransaction(User $user, Transaction $transaction): bool
    {
        // Agents can only edit transactions they personally created
        if ($user->hasRole(Roles::AGENT)) {
            return $transaction->agent_id === $user->id;
        }

        // Admin, supervisor, and branch manager can edit all transactions
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can delete a transaction.
     */
    public function deleteTransaction(User $user, Transaction $transaction): bool
    {
        // Agents can only delete transactions they personally created
        if ($user->hasRole(Roles::AGENT)) {
            return $transaction->agent_id === $user->id;
        }

        // Admin can delete any transaction
        return $user->hasRole(Roles::ADMIN);
    }

    /**
     * Determine whether the agent can view profit reports.
     */
    public function viewProfitReports(User $user): bool
    {
        // Agents cannot view company-wide profit reports
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        // Admin, supervisor, and branch manager can view profit reports
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can modify safe or line balances.
     */
    public function modifyBalances(User $user): bool
    {
        // Agents cannot modify safe balance or line balance
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        // Only Admin and Branch Manager can modify balances
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can view the supervisor dashboard.
     */
    public function viewSupervisorDashboard(User $user): bool
    {
        // Agents cannot view supervisor dashboard
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }

    /**
     * Determine whether the agent can access branch reports.
     */
    public function viewBranchReports(User $user): bool
    {
        // Agents cannot access reports covering other branches
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can modify customer data.
     */
    public function modifyCustomerData(User $user, $customerId): bool
    {
        // Agents can only modify customer data related to their own operations
        // This would require additional logic to check if the customer is related to the agent's transactions
        if ($user->hasRole(Roles::AGENT)) {
            // Implementation would depend on how customers are linked to transactions/agents
            // For now, we'll return false to be restrictive
            return false;
        }

        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can approve or reject pending transactions.
     */
    public function approveRejectTransactions(User $user): bool
    {
        // Agents cannot approve or reject pending transactions
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can create or modify user permissions.
     */
    public function manageUserPermissions(User $user): bool
    {
        // Agents cannot create new agents or change user permissions
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        return $user->hasRole(Roles::ADMIN);
    }

    /**
     * Determine whether the agent can manage users (create, update, read, delete).
     */
    public function manageUsers(User $user): bool
    {
        // Agents cannot manage users at all
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        // Other admin roles can manage users based on their level
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }

    /**
     * Determine whether the agent can view the user list.
     */
    public function viewUserList(User $user): bool
    {
        // Agents cannot view the user list
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
            $user->hasRole(Roles::BRANCH_MANAGER);
    }
}
