# Line Usage Reset Feature Documentation

## Overview

This feature ensures that daily and monthly usage statistics for SIM lines are automatically reset at appropriate times:

- **Daily Usage**: Reset to zero every day at midnight
- **Monthly Usage**: Reset to zero at the start of each month
- **Frozen Lines**: Automatically unfrozen daily

## Implementation Details

The functionality is implemented in the `UnfreezeLinesAndResetDailyBalance` command, which is scheduled to run daily at midnight.

### What the Command Does

1. **Daily Reset (Every day at 00:00)**
   - Updates `daily_starting_balance` to the current balance
   - Unfreezes any lines that were frozen due to exceeding limits

2. **Monthly Reset (On the first day of each month at 00:00)**
   - Performs all daily reset operations
   - Additionally updates `starting_balance` to the current balance

### Usage and Limit Calculation Logic

The system calculates usage and remaining values as follows:

1. **Daily Usage Calculation**:
   ```
   daily_usage = current_balance - daily_starting_balance
   ```
   This represents how much the balance has increased since the start of the day.

2. **Monthly Usage Calculation**:
   ```
   monthly_usage = current_balance - starting_balance
   ```
   This represents how much the balance has increased since the start of the month.

3. **Daily Remaining Calculation**:
   ```
   daily_remaining = daily_limit - current_balance
   ```
   This is the remaining amount that can be received in a day, calculated as the limit minus the current balance.

4. **Monthly Remaining Calculation**:
   ```
   monthly_remaining = monthly_limit - current_balance
   ```
   This is the remaining amount that can be received in a month, calculated as the limit minus the current balance.

### Technical Implementation

- The command is registered in `app/Console/Kernel.php`
- It runs in the background to prevent blocking other scheduled tasks
- Output is logged to `storage/logs/daily-reset.log` for monitoring
- Overlapping execution is prevented

## Testing

To test the functionality without waiting for the scheduled execution:

1. **Test Script**: Run the `scripts/test_line_usage_reset.php` script:
   ```
   php scripts/test_line_usage_reset.php
   ```
   By default, it runs in test mode and doesn't make actual database changes.

2. **Manual Command Execution**: 
   ```
   php artisan app:unfreeze-lines-and-reset-daily-balance
   ```

3. **Update Database View**:
   ```
   php scripts/update_line_utilization_view.php
   ```
   This updates the database view to use the new calculation logic.

## Troubleshooting

If issues occur with the line usage reset:

1. Check the log file: `storage/logs/daily-reset.log`
2. Verify that the scheduler is running correctly: `php artisan schedule:list`
3. Make sure the command is registered correctly in `app/Console/Kernel.php`

## Important Code Files

- `app/Console/Commands/UnfreezeLinesAndResetDailyBalance.php`: The main command implementation
- `app/Console/Kernel.php`: Where the command is scheduled
- `scripts/test_line_usage_reset.php`: Test script for manual verification
- `scripts/update_line_utilization_view.php`: Script to update the database view
- `resources/views/livewire/dashboard/agent.blade.php`: View file with calculation logic
- `resources/views/livewire/agent-dashboard.blade.php`: Alternative view with calculation logic

## Business Logic

This feature ensures accurate usage tracking for SIM lines by:

- Providing fresh daily usage statistics every day
- Allowing accurate monthly limits and tracking
- Automatically resetting frozen lines to prevent service disruption
- Maintaining proper historical balance tracking 