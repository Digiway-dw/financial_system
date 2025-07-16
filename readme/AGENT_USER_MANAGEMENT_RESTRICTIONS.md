# Agent User Management Restrictions

This document outlines the restrictions implemented to prevent agents from performing CRUD operations on users.

## Policy Restrictions

### UserPolicy

The `UserPolicy` has been updated to restrict agents from managing users:

- **viewAny**: Agents cannot view the list of all users.
- **view**: Agents can only view their own profile, not other users.
- **create**: Agents cannot create new users.
- **update**: Agents can only update their own profile, not other users.
- **delete**: Agents cannot delete any users, not even themselves.
- **restore**: Agents cannot restore deleted users.
- **forceDelete**: Agents cannot permanently delete users.

### AgentPolicy

Additional methods have been added to `AgentPolicy` to explicitly handle user management restrictions:

- **manageUsers**: Returns false for agents, preventing them from any user management.
- **viewUserList**: Returns false for agents, preventing them from viewing the user list.

## Gate Definitions

The following gates have been defined in `AuthorizationServiceProvider`:

- **manage-users**: Checks if the user is not an agent and has an appropriate role.
- **view-user-list**: Prevents agents from viewing the user list.
- **create-users**: Only admin can create users.
- **edit-users**: Prevents agents from editing any users.
- **delete-users**: Only admin can delete users.

## Middleware Protection

The `AgentDashboardAccess` middleware has been updated to:

- Block access to any routes containing `/users`, `/user`, or `/staff`.
- Allow agents to access only their own profile by checking the user ID in the route.
- Redirect agents to the agent dashboard with an error message if they attempt to access restricted user management routes.

## Implementation Rules

1. Agents cannot view the list of users in the system.
2. Agents cannot create new users.
3. Agents cannot update or modify other users.
4. Agents cannot delete any users.
5. Agents can only view and update their own profile.

## Verification Steps

To verify these restrictions are working correctly:

1. Log in as an agent user.
2. Attempt to access the user list (should be redirected).
3. Attempt to create a new user (should be denied).
4. Attempt to update another user's profile (should be denied).
5. Attempt to delete a user (should be denied).
6. Verify the agent can view and update their own profile.
7. Check that user management UI elements are hidden from the agent's view.

## UI Considerations

All UI elements related to user management (buttons, links, menu items) should be hidden from agents using the `@can` directive with the appropriate gates. For example:

```blade
@can('view-user-list')
    <a href="{{ route('users.index') }}">Manage Users</a>
@endcan
```
