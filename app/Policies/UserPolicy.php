<?php

namespace App\Policies;

use App\Domain\Entities\User;
use App\Constants\Roles;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Only admin, branch manager, and general supervisor can view users
        return $user->hasRole(Roles::ADMIN) ||
            $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Agent can only view their own profile
        if ($user->hasRole(Roles::AGENT)) {
            return $user->id === $model->id;
        }

        // Admins can view any user
        if ($user->hasRole(Roles::ADMIN)) {
            return true;
        }
        // Branch manager and general supervisor can view any user
        return $user->hasRole(Roles::BRANCH_MANAGER) ||
            $user->hasRole(Roles::GENERAL_SUPERVISOR);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only admin can create users
        return $user->hasRole(Roles::ADMIN);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Agents can only update their own profile
        if ($user->hasRole(Roles::AGENT)) {
            return $user->id === $model->id;
        }

        // Only admin@financial.system can update other admins
        if ($user->hasRole(Roles::ADMIN)) {
            if ($model->hasRole(Roles::ADMIN)) {
                return $user->email === 'admin@financial.system' && $user->id !== $model->id;
            }
            return $user->id === $model->id;
        }
        // Branch manager can update users in their branch
        if ($user->hasRole(Roles::BRANCH_MANAGER)) {
            return $model->branch_id === $user->branch_id;
        }
        // General supervisor can update their own profile
        if ($user->hasRole(Roles::GENERAL_SUPERVISOR)) {
            return $user->id === $model->id;
        }
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent deletion of the main admin account
        if ($model->email === 'admin@financial.system') {
            return false;
        }
        // Agents cannot delete any user, not even themselves
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }
        // Only admin@financial.system can delete other admins
        if ($user->hasRole(Roles::ADMIN)) {
            if ($model->hasRole(Roles::ADMIN)) {
                return $user->email === 'admin@financial.system' && $user->id !== $model->id;
            }
            return true;
        }
        // Supervisor can delete any user except admins and supervisors
        if ($user->hasRole(Roles::GENERAL_SUPERVISOR)) {
            return !$model->hasRole(Roles::ADMIN) && !$model->hasRole(Roles::GENERAL_SUPERVISOR);
        }
        // Others cannot delete
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        // Prevent restoring the main admin account (if ever soft deleted)
        if ($model->email === 'admin@financial.system') {
            return false;
        }
        // Agents cannot restore users
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        // Only admin can restore users
        return $user->hasRole(Roles::ADMIN);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Prevent force deleting the main admin account
        if ($model->email === 'admin@financial.system') {
            return false;
        }
        // Agents cannot force delete users
        if ($user->hasRole(Roles::AGENT)) {
            return false;
        }

        // Only admin can force delete users
        return $user->hasRole(Roles::ADMIN);
    }
}
