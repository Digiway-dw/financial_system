# Agent Access Restrictions

## Overview
This document outlines the access restrictions for agents in the financial system, with a specific focus on preventing agents from accessing user management functionality.

## Key Restrictions

1. **User Management Restriction**
   - Agents cannot view the user list
   - Agents cannot create users
   - Agents cannot edit users (except their own profile)
   - Agents cannot delete users

2. **Dashboard Restrictions**
   - Agents can only access the agent dashboard
   - Agents cannot access admin, supervisor, branch manager, or auditor dashboards

3. **Transaction Restrictions**
   - Agents can only view their own transactions
   - Agents cannot approve transactions from other agents or trainees

4. **Financial Data Restrictions**
   - Agents cannot access company financial reports
   - Agents cannot modify safe or line balances
   - Agents can only initiate (but not approve) safe transfers

## Implementation Details

### Middleware Protection
The `AgentDashboardAccess` middleware enforces these restrictions by:
- Redirecting agents to their dashboard if they try to access other dashboards
- Restricting agents from viewing transactions they didn't create
- Blocking access to company financial reports
- Blocking access to safe/line balance modification
- Blocking access to user management routes (with an exception for viewing their own profile)

### Gate Definitions
The authorization gates in `AuthorizationServiceProvider` define permissions that enforce:
- `view-user-list` - Not granted to agents
- `create-users` - Not granted to agents
- `edit-users` - Not granted to agents
- `delete-users` - Not granted to agents
- `manage-users` - Not granted to agents
- `view-agent-dashboard` - Only granted to agents

### Route Protection
User management routes are protected with middleware:
- `users.index` - Protected by `can:view-user-list` middleware
- `users.create` - Protected by `can:create-users` middleware
- `users.edit` - Protected by `can:edit-users` middleware

## Recent Fixes
1. Removed the global `BypassAuthorization` middleware that was overriding all gate checks and allowing unauthorized access
2. Ensured the `AgentDashboardAccess` middleware is properly registered in the middleware stack
3. Verified all permissions are working correctly with a test command

## Verification
A test command `php artisan test:agent-access` is available to verify that agents cannot access unauthorized functionality.

## Notes
- Agents can still view their own profile via the `/users/{userId}/view` route, but only if the userId matches their own ID
- All other user management access will redirect to the agent dashboard with an error message
