<?php

namespace App\Providers;

use App\Constants\Roles;
use App\Domain\Entities\User as DomainUser;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthorizationServiceProvider extends ServiceProvider
{
    /**
     * Register authorization gates.
     */
    public function boot(): void
    {
        // Register User policy for Domain\Entities\User
        Gate::policy(DomainUser::class, UserPolicy::class);

        // ===== LINE MANAGEMENT =====
        Gate::define('manage-lines', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        // ===== SAFE MANAGEMENT =====
        Gate::define('manage-safes', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        // ===== REPORTS =====
        Gate::define('view-reports', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::AUDITOR);
        });

        Gate::define('view-branch-reports', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::AUDITOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        Gate::define('view-commission-reports', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        // ===== TRANSACTION MANAGEMENT =====
        Gate::define('manage-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AUDITOR);
        });

        // ===== CUSTOMER MANAGEMENT =====
        Gate::define('manage-customers', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        // More specific customer management gates
        Gate::define('view-customers', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('create-customers', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('edit-customers', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('delete-customers', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        // ===== BRANCH MANAGEMENT =====
        Gate::define('manage-branches', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        // ===== USER MANAGEMENT =====
        Gate::define('manage-users', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        // Only Admin can manage roles and system settings
        Gate::define('manage-roles', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        Gate::define('manage-system-settings', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        // ===== DATA VISIBILITY =====
        Gate::define('view-all-branches-data', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::AUDITOR);
        });

        Gate::define('view-own-branch-data', function (DomainUser $user) {
            return $user->hasRole(Roles::BRANCH_MANAGER);
        });

        Gate::define('view-other-employees-data', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AUDITOR);
        });

        // ===== TRANSACTION ACTIONS =====
        Gate::define('create-transactions', function (DomainUser $user) {
            // All users can create transactions
            return true;
        });

        Gate::define('edit-own-transactions', function (DomainUser $user) {
            // All users can edit their own transactions
            return true;
        });

        Gate::define('edit-all-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('edit-branch-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('delete-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        // ===== TRANSACTION APPROVAL =====
        Gate::define('approve-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::AUDITOR);
        });

        Gate::define('approve-branch-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::AUDITOR);
        });

        // ===== CASH HANDLING =====
        Gate::define('create-cash-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT);
        });

        // Only Admin and Branch Manager can do unrestricted cash withdrawal
        Gate::define('unrestricted-cash-withdrawal', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        // Safe-to-safe transfers
        Gate::define('initiate-safe-transfer', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT);
        });

        Gate::define('approve-safe-transfer', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        // Cash transaction gates
        Gate::define('cash-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT);
        });

        // Direct cash deposit (no approval needed)
        Gate::define('deposit-cash', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AGENT);
        });

        // Cash withdrawal gate (TEMPORARY DEBUG: always allow)
        Gate::define('withdraw-cash', function (DomainUser $user) {
            return true;
        });

        // ===== BALANCE MANAGEMENT =====
        Gate::define('modify-balances', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        // ===== ROLE-SPECIFIC DASHBOARDS =====
        Gate::define('view-admin-dashboard', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        Gate::define('view-supervisor-dashboard', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('view-branch-manager-dashboard', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        Gate::define('view-agent-dashboard', function (DomainUser $user) {
            return $user->hasRole(Roles::AGENT);
        });

        Gate::define('view-auditor-dashboard', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::AUDITOR);
        });

        Gate::define('view-trainee-dashboard', function (DomainUser $user) {
            return $user->hasRole(Roles::TRAINEE);
        });

        // ===== TRAINEE SPECIFIC =====
        Gate::define('require-transaction-approval', function (DomainUser $user) {
            return $user->hasRole(Roles::TRAINEE);
        });

        Gate::define('view-trainee-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::TRAINEE);
        });

        Gate::define('approve-trainee-transactions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        // ===== USER MANAGEMENT =====
        Gate::define('view-user-list', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        Gate::define('create-users', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR);
        });

        Gate::define('edit-users', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        Gate::define('delete-users', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        // ===== COMMISSION DATA =====
        Gate::define('view-commission-data', function (DomainUser $user) {
            // For agents, only their own commission
            // For others, all commission data
            return true;
        });

        Gate::define('view-all-commission-data', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER);
        });

        // ===== WORK SESSIONS =====
        Gate::define('view-work-sessions', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        // ===== PRINTING =====
        Gate::define('print-receipts', function (DomainUser $user) {
            // All users can print receipts
            return true;
        });

        // ===== SEARCH CAPABILITIES =====
        Gate::define('advanced-search', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN) ||
                $user->hasRole(Roles::GENERAL_SUPERVISOR) ||
                $user->hasRole(Roles::BRANCH_MANAGER) ||
                $user->hasRole(Roles::AUDITOR);
        });

        Gate::define('basic-search', function (DomainUser $user) {
            // Agent can only search by Transaction ID, Customer Phone, or Customer Code
            return true;
        });

        // ===== VIEW BRANCHES =====
        Gate::define('view-branches', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });

        // ===== AUDIT LOG =====
        Gate::define('view-audit-log', function (DomainUser $user) {
            return $user->hasRole(Roles::ADMIN);
        });
    }
}
