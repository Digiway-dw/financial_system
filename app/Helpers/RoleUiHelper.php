<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Constants\Roles;

/**
 * Helper class for role-based UI consistency
 */
class RoleUiHelper
{
    /**
     * Get the appropriate dashboard component based on user role
     *
     * @return string
     */
    public static function getDashboardComponent(): string
    {
        $user = Auth::user();

        if (!$user) {
            return 'dashboard.guest';
        }

        if (Gate::forUser($user)->allows('view-admin-dashboard')) {
            return 'dashboard.admin';
        }

        if (Gate::forUser($user)->allows('view-supervisor-dashboard')) {
            return 'dashboard.general_supervisor';
        }

        if (Gate::forUser($user)->allows('view-branch-manager-dashboard')) {
            return 'dashboard.branch_manager';
        }

        if (Gate::forUser($user)->allows('view-auditor-dashboard')) {
            return 'dashboard.auditor';
        }

        if (Gate::forUser($user)->allows('view-agent-dashboard')) {
            return 'dashboard.agent';
        }

        if (Gate::forUser($user)->allows('view-trainee-dashboard')) {
            return 'dashboard.trainee';
        }

        // Default dashboard for undefined roles
        return 'dashboard.default';
    }

    /**
     * Get the transaction action buttons based on user role
     * 
     * @return array
     */
    public static function getTransactionActionButtons(): array
    {
        $user = Auth::user();
        $buttons = [];

        if (!$user) {
            return $buttons;
        }

        // Send money - most roles can send money
        if (Gate::forUser($user)->allows('create-transactions')) {
            $buttons[] = [
                'route' => 'transactions.send',
                'label' => 'Send Money',
                'description' => 'Create outgoing transfer',
                'icon' => 'paper-airplane',
                'color' => 'blue'
            ];
        }

        // Receive money - most roles can receive money
        if (Gate::forUser($user)->allows('create-transactions')) {
            $buttons[] = [
                'route' => 'transactions.receive',
                'label' => 'Receive Money',
                'description' => 'Process incoming transfer',
                'icon' => 'arrow-down-tray',
                'color' => 'green'
            ];
        }

        // Cash transactions - only certain roles
        if (Gate::forUser($user)->allows('create-cash-transactions')) {
            $buttons[] = [
                'route' => 'transactions.cash',
                'label' => 'Cash Transaction',
                'description' => 'Handle cash operations',
                'icon' => 'currency-dollar',
                'color' => 'yellow'
            ];
        }

        // Safe transfers - only certain roles
        if (Gate::forUser($user)->allows('initiate-safe-transfer')) {
            $buttons[] = [
                'route' => 'safes.transfer',
                'label' => 'Safe Transfer',
                'description' => 'Transfer between safes',
                'icon' => 'arrows-right-left',
                'color' => 'purple'
            ];
        }

        // Pending approvals - only for managers and supervisors
        if (Gate::forUser($user)->allows('approve-transactions')) {
            $buttons[] = [
                'route' => 'transactions.pending',
                'label' => 'Pending Approvals',
                'description' => 'Review and approve transactions',
                'icon' => 'clock',
                'color' => 'red'
            ];
        }

        return $buttons;
    }

    /**
     * Check if the current user has any management permissions
     * 
     * @return bool
     */
    public static function hasManagementPermissions(): bool
    {
        $user = Auth::user();

        if (!$user) {
            return false;
        }

        return Gate::forUser($user)->allows('manage-users') ||
            Gate::forUser($user)->allows('manage-branches') ||
            Gate::forUser($user)->allows('manage-lines') ||
            Gate::forUser($user)->allows('manage-safes') ||
            Gate::forUser($user)->allows('manage-roles') ||
            Gate::forUser($user)->allows('manage-system-settings');
    }

    /**
     * Get the roles display information for UI consistency
     * 
     * @return array
     */
    public static function getRolesDisplayInfo(): array
    {
        return [
            Roles::ADMIN => [
                'label' => 'Administrator',
                'badge_color' => 'red',
                'icon' => 'cog'
            ],
            Roles::GENERAL_SUPERVISOR => [
                'label' => 'General Supervisor',
                'badge_color' => 'purple',
                'icon' => 'shield-check'
            ],
            Roles::BRANCH_MANAGER => [
                'label' => 'Branch Manager',
                'badge_color' => 'blue',
                'icon' => 'office-building'
            ],
            Roles::AGENT => [
                'label' => 'Agent',
                'badge_color' => 'green',
                'icon' => 'user'
            ],
            Roles::TRAINEE => [
                'label' => 'Trainee Agent',
                'badge_color' => 'yellow',
                'icon' => 'academic-cap'
            ],
            Roles::AUDITOR => [
                'label' => 'Auditor',
                'badge_color' => 'gray',
                'icon' => 'clipboard-check'
            ]
        ];
    }

    /**
     * Get display info for the current user's role
     * 
     * @return array|null
     */
    public static function getCurrentUserRoleInfo(): ?array
    {
        $user = Auth::user();

        if (!$user || !$user->roles->first()) {
            return null;
        }

        $roleName = $user->roles->first()->name;
        $rolesInfo = self::getRolesDisplayInfo();

        return $rolesInfo[$roleName] ?? null;
    }
}
