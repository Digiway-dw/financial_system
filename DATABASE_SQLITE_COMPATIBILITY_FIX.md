# SQLite Constraint Compatibility Fix

## Issue
The database migrations that add CHECK constraints to the transactions table were causing errors when running tests with SQLite. This is because SQLite has different syntax requirements for adding constraints compared to MySQL/PostgreSQL.

## Solution
We've implemented the following fixes to ensure compatibility with both production databases and SQLite for testing:

1. Modified existing constraint migrations to check the database driver before executing constraint-specific SQL
2. Added conditional logic that skips constraint creation for SQLite connections
3. Created a new migration `2025_07_11_200000_fix_sqlite_constraints.php` that properly handles both SQLite and MySQL/PostgreSQL

## Technical Details

### Driver Detection
```php
if (DB::connection()->getDriverName() !== 'sqlite') {
    // Execute MySQL/PostgreSQL compatible code
}
```

### Constraint Creation for Production
For production databases (MySQL/PostgreSQL), we maintain the original constraint creation logic with proper existence checks to prevent duplicate constraints:

```php
$checkConstraintExists = function ($table, $constraintName) {
    $schema = DB::connection()->getDatabaseName();
    $constraintExists = DB::select("
        SELECT COUNT(*) as count 
        FROM information_schema.table_constraints 
        WHERE constraint_schema = '$schema' 
        AND table_name = '$table' 
        AND constraint_name = '$constraintName'
    ");

    return $constraintExists[0]->count > 0;
};

if (!$checkConstraintExists('transactions', 'chk_transaction_amount_positive')) {
    DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_transaction_amount_positive CHECK (amount > 0)');
}
```

## Impact
This fix ensures:
1. Tests using SQLite run without constraint-related errors
2. Production databases still receive the proper constraints for data integrity
3. Both new and existing installations work correctly

## Future Considerations
When creating new migrations that involve database-specific features:
1. Always check the database driver before executing driver-specific SQL
2. Use Schema Builder methods when possible instead of raw SQL for better cross-database compatibility
3. Consider creating separate migrations for different database engines when necessary

## Related Changes
- Updated `WorkSessionTest.php` to properly test session functionality
- Fixed database compatibility issues in existing constraints
- Added comprehensive documentation for the work sessions feature
