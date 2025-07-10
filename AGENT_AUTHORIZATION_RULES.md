# Agent Authorization Rules

This document outlines the authorization rules for users with the 'agent' role in the financial system.

## Agent Permissions

Agents like Ahmed are restricted to the following permissions:

### Allowed Actions

1. **Dashboard Access**
   - Can only access their own Agent Dashboard
   - Cannot view Supervisor Dashboard or other administrative dashboards

2. **Transactions**
   - Can view only transactions they personally created
   - Cannot see other agents' transactions
   - Can create new transactions
   - Can only edit or delete their own transactions

3. **Line Management**
   - Can view the balance of mobile lines assigned to them only
   - Cannot modify any Line balance (reserved for Branch Manager or Admin)

4. **Performance Metrics**
   - Can view their own transaction statistics (count and commission earned)
   - Cannot view company-wide profit or financial reports

### Restricted Actions

1. **Safe Management**
   - Cannot modify Safe balance (restricted to Branch Manager or Admin)

2. **Branch Access**
   - Cannot access reports covering other branches

3. **Customer Data**
   - Cannot modify customer data not related to their own operations

4. **Transaction Approval**
   - Cannot approve or reject pending transactions (only Branch Manager or Admin can do this)

5. **User Management**
   - Cannot create new Agents or change any user permissions

## Implementation Details

These rules are enforced through:

1. **Gates**: Authorization gates in `AuthorizationServiceProvider` define permissions based on user roles
   - `view-agent-dashboard`: Only agents can access their dashboard
   - `view-all-branches-data`: Admins and supervisors can view all branch data
   - `view-own-branch-data`: Branch managers can view their branch data
   - `view-supervisor-dashboard`: Prevents agents from accessing supervisor dashboards
   - `view-profit-reports`: Prevents agents from viewing financial reports
   - `modify-balances`: Restricts balance modifications to admins and branch managers
   - `approve-transactions`: Prevents agents from approving transactions
   - `manage-user-permissions`: Restricts user management to admins

2. **Middleware**: `AgentDashboardAccess` middleware restricts navigation
   - Redirects agents who try to access unauthorized pages
   - Prevents access to other users' transactions
   - Blocks access to financial reports and balance modification pages

3. **Use Cases**: Transaction-related use cases enforce agent permissions
   - `ListTransactions`: Filters transactions so agents only see their own
   - Uses Gate facade to check permissions consistently

## Verification

To verify the implementation is working correctly:

1. Login as an agent user
2. Confirm you can only view your own dashboard
3. Verify you can only see transactions you created
4. Check that you cannot access or modify safe balances
5. Ensure you cannot access supervisor dashboard or reports
