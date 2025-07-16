# Safe Balance Attribute Fix

## Issue Summary
There were multiple instances in the application where the Safe model's balance was being incorrectly referenced as `balance` instead of the correct attribute name `current_balance`. This was causing "undefined attribute" errors when trying to display or use safe balances in various components.

## Changes Made

### 1. Transaction Edit View
Updated the safe balance reference in the safe dropdown menu:
- Changed from `{{ $safe->balance }}` to `{{ $safe->current_balance }}`

### 2. Withdrawal Component 
Fixed the balance check when validating if a safe has sufficient funds:
- Changed from `$safe->balance < $this->amount` to `$safe->current_balance < $this->amount`

### 3. Withdrawal View
Updated the balance display in the safe selection dropdown:
- Changed from `{{ number_format($safe->balance, 2) }}` to `{{ number_format($safe->current_balance, 2) }}`

## Model Structure
- Safe model uses `current_balance` as the attribute name
- Customer model uses `balance` as the attribute name (this is correct and unchanged)

## Additional Notes
- The Safe model consistently uses `current_balance` in database constraints, migrations, and repositories
- This fix ensures consistency across the application and prevents "undefined attribute" errors
- All views and components now correctly reference the Safe model's balance attribute

## Related Files
- `resources/views/livewire/transactions/edit.blade.php`
- `app/Livewire/Transactions/Withdrawal.php`
- `resources/views/livewire/transactions/withdrawal.blade.php`
