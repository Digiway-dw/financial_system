<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Skip for SQLite (used in testing)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Add enhanced constraints and data validation

        // Enhance transactions table with better constraints
        // Add check constraints for amounts
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_transaction_amount_positive CHECK (amount > 0)');
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_commission_non_negative CHECK (commission >= 0)');
        DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_deduction_non_negative CHECK (deduction >= 0)');

        // Add constraint for transaction types
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment'))");

        // Add constraint for status values
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_status CHECK (status IN ('pending', 'completed', 'rejected', 'cancelled'))");

        // Enhance users table with better validation
        // Add constraint for salary (must be positive if provided)
        DB::statement('ALTER TABLE users ADD CONSTRAINT chk_user_salary_positive CHECK (salary IS NULL OR salary > 0)');

        // Add constraint for national number format (14 digits)
        DB::statement("ALTER TABLE users ADD CONSTRAINT chk_national_number_format CHECK (national_number IS NULL OR (LENGTH(national_number) = 14 AND national_number REGEXP '^[0-9]+$'))");

        // Enhance customers table with validation
        // Add constraint for balance (can be negative for debt tracking)
        DB::statement('ALTER TABLE customers ADD CONSTRAINT chk_customer_balance_reasonable CHECK (balance >= -1000000 AND balance <= 1000000)');

        // Add constraint for gender values
        DB::statement("ALTER TABLE customers ADD CONSTRAINT chk_customer_gender CHECK (gender IS NULL OR gender IN ('male', 'female'))");

        // Enhance safes table with validation
        // Add constraint for safe balance (should not go extremely negative)
        DB::statement('ALTER TABLE safes ADD CONSTRAINT chk_safe_balance_reasonable CHECK (current_balance >= -10000000)');

        // Add constraint for safe type
        DB::statement("ALTER TABLE safes ADD CONSTRAINT chk_safe_type CHECK (type IN ('branch', 'main', 'temporary', 'reserve'))");

        // Enhance lines table with validation (quote table name for reserved word)
        // Add constraints for limits and usage
        DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_limits_positive CHECK (daily_limit > 0 AND monthly_limit > 0)');
        DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_usage_non_negative CHECK (daily_usage >= 0 AND monthly_usage >= 0)');
        DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_usage_within_limits CHECK (daily_usage <= daily_limit AND monthly_usage <= monthly_limit)');

        // Add constraint for network values
        DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_network CHECK (network IN ('orange', 'vodafone', 'etisalat', 'we', 'Fawry'))");

        // Add constraint for status values
        DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_status CHECK (status IN ('active', 'inactive', 'suspended', 'maintenance'))");

        // Enhance branches table with validation
        // Ensure branch code follows pattern (2 letters + 3 digits)
        DB::statement("ALTER TABLE branches ADD CONSTRAINT chk_branch_code_format CHECK (branch_code REGEXP '^[A-Z]{2}[0-9]{3}$')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Skip for SQLite (used in testing)
        if (DB::connection()->getDriverName() === 'sqlite') {
            return;
        }

        // Remove constraints in reverse order
        try { DB::statement('ALTER TABLE transactions DROP CHECK chk_transaction_amount_positive'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE transactions DROP CHECK chk_commission_non_negative'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE transactions DROP CHECK chk_deduction_non_negative'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE transactions DROP CHECK chk_transaction_type'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE transactions DROP CHECK chk_transaction_status'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE users DROP CHECK chk_user_salary_positive'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE users DROP CHECK chk_national_number_format'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE customers DROP CHECK chk_customer_balance_reasonable'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE customers DROP CHECK chk_customer_gender'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE safes DROP CHECK chk_safe_balance_reasonable'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE safes DROP CHECK chk_safe_type'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE `lines` DROP CHECK chk_line_limits_positive'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE `lines` DROP CHECK chk_line_usage_non_negative'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE `lines` DROP CHECK chk_line_usage_within_limits'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE `lines` DROP CHECK chk_line_network'); } catch (\Exception $e) {}
        try { DB::statement('ALTER TABLE `lines` DROP CHECK chk_line_status'); } catch (\Exception $e) {}

        try { DB::statement('ALTER TABLE branches DROP CHECK chk_branch_code_format'); } catch (\Exception $e) {}
    }
};
