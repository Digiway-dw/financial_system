<?php

namespace App\Policies;

use App\Constants\Roles;
use App\Domain\Entities\User;
use App\Models\Domain\Entities\Customer;
use Illuminate\Auth\Access\HandlesAuthorization;

class CustomerPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the customer list.
     */
    public function viewAny(User $user): bool
    {
        // Auditors, Agents, Branch Managers, and Admins can view customer lists
        return $user->hasRole(Roles::AUDITOR) ||
            $user->hasRole(Roles::AGENT) ||
            $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }

    /**
     * Determine whether the user can view a specific customer.
     */
    public function view(User $user, Customer $customer): bool
    {
        // Auditors, Agents, Branch Managers, and Admins can view any customer
        return $user->hasRole(Roles::AUDITOR) ||
            $user->hasRole(Roles::AGENT) ||
            $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }

    /**
     * Determine whether the user can create customers.
     */
    public function create(User $user): bool
    {
        // Auditors cannot create customers
        return $user->hasRole(Roles::AGENT) ||
            $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }

    /**
     * Determine whether the user can update the customer.
     */
    public function update(User $user, Customer $customer): bool
    {
        // Auditors cannot update customers
        return $user->hasRole(Roles::AGENT) ||
            $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }

    /**
     * Determine whether the user can delete the customer.
     */
    public function delete(User $user, Customer $customer): bool
    {
        // Auditors cannot delete customers
        return $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }
}
