# Role-Based Authorization and UI Consistency Guide

## Overview

This document provides a comprehensive guide to the role-based authorization system and UI consistency patterns implemented in the Financial System application. The system enforces strict permission controls based on user roles while providing a consistent and role-appropriate user interface.

## Authorization Components

### 1. Gate Definitions

The core of our authorization system is defined in the `AuthorizationServiceProvider` which establishes all permission gates:

- **Data Visibility Gates**: `view-all-branches-data`, `view-own-branch-data`
- **Role-Specific Dashboard Gates**: `view-admin-dashboard`, `view-supervisor-dashboard`, etc.
- **Transaction Gates**: `create-transactions`, `approve-transactions`, etc.
- **Cash Handling Gates**: `deposit-cash`, `initiate-safe-transfer`, etc.
- **User Management Gates**: `view-user-list`, `create-users`, etc.

### 2. Middleware

Several middleware components enforce access restrictions:

- `AgentDashboardAccess`: Restricts agents from accessing unauthorized areas
- `BypassAuthorization`: Now properly configured to respect permission gates

### 3. Policies

Policy classes enforce model-specific authorization:

- `UserPolicy`: Controls user management permissions
- `AgentPolicy`: Controls transaction and line access for agents
- `TraineePolicy`: Controls trainee-specific permissions

## UI Consistency Framework

### 1. RoleUiHelper

The `RoleUiHelper` class centralizes UI elements based on roles:

- `getDashboardComponent()`: Returns the appropriate dashboard view for each role
- `getTransactionActionButtons()`: Returns role-appropriate action buttons
- `hasManagementPermissions()`: Checks if user has any management permissions
- `getRolesDisplayInfo()`: Provides standardized display info for each role

### 2. Blade Templates

All blade templates should follow these patterns:

- Use `@can` directives to conditionally show UI elements
- Use the RoleUiHelper for consistent UI presentation
- Include appropriate error messages for unauthorized actions

## Role Permissions Summary

| Permission | Admin | General Supervisor | Branch Manager | Agent | Trainee | Auditor |
|------------|:-----:|:------------------:|:--------------:|:-----:|:-------:|:-------:|
| View All Branches | ✅ | ✅ | ❌ | ❌ | ❌ | ✅ |
| View Own Branch | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ |
| Create Users | ✅ | ✅ | ❌ | ❌ | ❌ | ❌ |
| Edit Users | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Manage Safes | ✅ | ✅ | ✅ | ❌ | ❌ | ❌ |
| Safe Transfers | ✅ | ✅ | ✅ | ✅ | ❌ | ❌ |
| Cash Transactions | ✅ | ✅ | ✅ | ✅ | ✅ | ❌ |
| View Reports | ✅ | ✅ | ✅ | ❌ | ❌ | ✅ |
| Modify Balances | ✅ | ❌ | ✅ | ❌ | ❌ | ❌ |

## Implementation Guidelines

### Transaction Visibility

Transaction visibility is controlled in the `ListTransactions` class, which applies filters based on the user's role:

- **Admin/Supervisor/Auditor**: See all transactions across branches
- **Branch Manager**: See only transactions from their branch
- **Agent/Trainee**: See only their own transactions

### UI Element Visibility

UI elements should only be shown to users with appropriate permissions:

```blade
@can('create-users')
    <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
@endcan
```

### Custom Dashboard for Each Role

Each role has a dedicated dashboard component:

- `dashboard.admin.blade.php`
- `dashboard.general_supervisor.blade.php`
- `dashboard.branch_manager.blade.php`
- `dashboard.agent.blade.php`
- `dashboard.trainee.blade.php`
- `dashboard.auditor.blade.php`

## Testing Authorization

Use the `php artisan test:agent-access` command to verify that role restrictions are properly enforced.

## Best Practices

1. **Always use Gates**: Prefer `Gate::allows()` over direct role checks
2. **Be explicit with permissions**: Define specific permissions for specific actions
3. **Use the RoleUiHelper**: Maintain consistent UI based on roles
4. **Check permissions at multiple levels**: Controller, Blade, and Policy
5. **Keep role definitions in Constants**: Use the `Roles` class for role names
