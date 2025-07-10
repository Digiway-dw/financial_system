# Role-Based Authorization Setup Guide

This guide explains how to set up the role-based authorization system in the Financial System.

## Available Roles

The system uses the following roles as defined in `app/Constants/Roles.php`:

1. **Admin** - Full system access with unrestricted cash withdrawal
2. **General Supervisor** - Cross-branch oversight without role management
3. **Auditor** - Transaction approval and audit capabilities
4. **Branch Manager** - Branch-level management and approvals
5. **Agent** - Transaction processing with restricted withdrawals
6. **Trainee** - Supervised transaction processing requiring approval

## Setting Up Roles

### Option 1: Using the Command (Recommended)

Run the following command to set up all roles and create demo users:

```
php artisan system:setup-roles
```

This will:
1. Run the roles migration
2. Seed the roles table
3. Set up permissions
4. Create demo users for each role

### Option 2: Manual Setup

If you prefer to set up roles manually, follow these steps:

1. Run the migration:
   ```
   php artisan migrate --path=database/migrations/2025_07_10_000001_add_system_roles.php
   ```

2. Run the seeders:
   ```
   php artisan db:seed --class=RoleSeeder
   php artisan db:seed --class=EnhancedPermissionsSeeder
   php artisan db:seed --class=FinancialSystemSeeder
   ```

## Demo Users

After running the setup, the following demo users will be available:

| Role              | Email                       | Password  |
|-------------------|----------------------------|-----------|
| Admin             | admin@financial.system     | password  |
| General Supervisor| supervisor@financial.system| password  |
| Auditor           | auditor@financial.system   | password  |
| Branch Manager    | manager@financial.system   | password  |
| Agent             | agent@financial.system     | password  |
| Trainee           | trainee@financial.system   | password  |

## Verifying Roles

To verify that roles are set up correctly, you can:

1. Log in with the admin credentials
2. Navigate to the Users section
3. Check that users have the correct roles assigned
4. Test the permissions by logging in as different users

## Troubleshooting

If you encounter issues with roles or permissions:

1. Make sure the migrations have run successfully:
   ```
   php artisan migrate:status
   ```

2. Check if roles exist in the database:
   ```
   php artisan tinker
   > \Spatie\Permission\Models\Role::all()->pluck('name');
   ```

3. Verify that the Constants\Roles class matches the roles in the database:
   ```
   php artisan tinker
   > \App\Constants\Roles::all();
   ```

4. Clear cache if necessary:
   ```
   php artisan cache:clear
   php artisan config:clear
   ```

## Further Customization

If you need to modify role permissions, edit the `EnhancedPermissionsSeeder.php` file and rerun the seeder:

```
php artisan db:seed --class=EnhancedPermissionsSeeder
```

To add new demo users or modify existing ones, edit the `createDemoUsers` method in `FinancialSystemSeeder.php`.
