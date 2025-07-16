# Database Migration Fix: Duplicate Column Error

## Issue
The migration was failing with the error:
```
SQLSTATE[42S21]: Column already exists: 1060 Duplicate column name 'notes'
```

## Root Cause
Two separate migration files were attempting to add the same column to the `transactions` table:
1. `2025_07_09_115116_add_notes_to_transactions_table.php`
2. `2025_07_09_150345_add_notes_to_transactions_table.php`

The first migration had already run successfully and added the column, causing the second migration to fail when it tried to add the same column.

## Solution
Updated the second migration file to include a check for the column's existence before attempting to add it:

```php
if (!Schema::hasColumn('transactions', 'notes')) {
    $table->text('notes')->nullable()->after('amount');
}
```

Similarly, updated the `down()` method to check if the column exists before trying to drop it:

```php
if (Schema::hasColumn('transactions', 'notes')) {
    $table->dropColumn('notes');
}
```

## Best Practices for Database Migrations

1. **Always check if a column/table exists before creating or modifying it**
   ```php
   if (!Schema::hasColumn('table_name', 'column_name')) {
       $table->type('column_name')->nullable();
   }
   ```

2. **Use unique and descriptive names for migration files**
   - Include the table name and the specific action being performed
   - Use timestamps to maintain chronological order

3. **Test migrations thoroughly**
   - Test both the `up()` and `down()` methods
   - Test migrations in sequence to ensure they work together

4. **Keep migrations focused**
   - Each migration should perform a single logical change
   - Avoid making multiple unrelated changes in a single migration

5. **Use database transactions**
   - Wrap complex migrations in transactions to ensure atomicity
   
   ```php
   DB::transaction(function () {
       // Migration logic here
   });
   ```

6. **Document complex migrations**
   - Add detailed comments explaining what the migration does and why
   - Document any prerequisites or dependencies

7. **Handle potential data loss carefully**
   - Be extremely cautious when dropping columns or tables
   - Add safeguards or backup mechanisms for destructive operations

8. **Version control your migrations**
   - Never modify an existing migration that has been deployed
   - Create a new migration to make additional changes

9. **Use appropriate column types and constraints**
   - Choose the most appropriate data types for your needs
   - Add constraints (unique, nullable, etc.) appropriately

10. **Remember idempotency**
    - Migrations should be runnable multiple times without error
    - Use existence checks to achieve this
