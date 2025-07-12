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
        // Add enhanced constraints and data validation
        // First check if constraints exist to avoid duplicate constraint errors

        // Helper function to check if a constraint exists
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

        // Enhance transactions table with better constraints
        // Add check constraints for amounts
        if (DB::connection()->getDriverName() !== 'sqlite' && !$checkConstraintExists('transactions', 'chk_transaction_amount_positive')) {
            DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_transaction_amount_positive CHECK (amount > 0)');
        }

        if (DB::connection()->getDriverName() !== 'sqlite' && !$checkConstraintExists('transactions', 'chk_commission_non_negative')) {
            DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_commission_non_negative CHECK (commission >= 0)');
        }

        if (DB::connection()->getDriverName() !== 'sqlite' && !$checkConstraintExists('transactions', 'chk_deduction_non_negative')) {
            DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_deduction_non_negative CHECK (deduction >= 0)');
        }

        // Add constraint for transaction types
        if (DB::connection()->getDriverName() !== 'sqlite' && !$checkConstraintExists('transactions', 'chk_transaction_type')) {
            DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment'))");
        }

        // Add constraint for status values
        if (!$checkConstraintExists('transactions', 'chk_transaction_status')) {
            DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_status CHECK (status IN ('pending', 'completed', 'rejected', 'cancelled'))");
        }

        // Enhance users table with better validation
        // Add constraint for salary (must be positive if provided)
        if (!$checkConstraintExists('users', 'chk_user_salary_positive')) {
            DB::statement('ALTER TABLE users ADD CONSTRAINT chk_user_salary_positive CHECK (salary IS NULL OR salary > 0)');
        }

        // Add constraint for national number format (14 digits)
        if (!$checkConstraintExists('users', 'chk_national_number_format')) {
            DB::statement("ALTER TABLE users ADD CONSTRAINT chk_national_number_format CHECK (national_number IS NULL OR (LENGTH(national_number) = 14 AND national_number REGEXP '^[0-9]+$'))");
        }

        // Enhance customers table with validation
        // Add constraint for balance (can be negative for debt tracking)
        if (!$checkConstraintExists('customers', 'chk_customer_balance_reasonable')) {
            DB::statement('ALTER TABLE customers ADD CONSTRAINT chk_customer_balance_reasonable CHECK (balance >= -1000000 AND balance <= 1000000)');
        }

        // Add constraint for gender values
        if (!$checkConstraintExists('customers', 'chk_customer_gender')) {
            DB::statement("ALTER TABLE customers ADD CONSTRAINT chk_customer_gender CHECK (gender IS NULL OR gender IN ('male', 'female'))");
        }

        // Enhance safes table with validation
        // Add constraint for safe balance (should not go extremely negative)
        if (!$checkConstraintExists('safes', 'chk_safe_balance_reasonable')) {
            DB::statement('ALTER TABLE safes ADD CONSTRAINT chk_safe_balance_reasonable CHECK (current_balance >= -10000000)');
        }

        // Add constraint for safe type
        if (!$checkConstraintExists('safes', 'chk_safe_type')) {
            DB::statement("ALTER TABLE safes ADD CONSTRAINT chk_safe_type CHECK (type IN ('branch', 'main', 'temporary', 'reserve'))");
        }

        // Enhance lines table with validation (quote table name for reserved word)
        // Add constraints for limits and usage
        if (!$checkConstraintExists('lines', 'chk_line_limits_positive')) {
            DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_limits_positive CHECK (daily_limit > 0 AND monthly_limit > 0)');
        }

        if (!$checkConstraintExists('lines', 'chk_line_usage_non_negative')) {
            DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_usage_non_negative CHECK (daily_usage >= 0 AND monthly_usage >= 0)');
        }

        if (!$checkConstraintExists('lines', 'chk_line_usage_within_limits')) {
            DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_usage_within_limits CHECK (daily_usage <= daily_limit AND monthly_usage <= monthly_limit)');
        }

        // Add constraint for network values
        if (!$checkConstraintExists('lines', 'chk_line_network')) {
            DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_network CHECK (network IN ('orange', 'vodafone', 'etisalat', 'we'))");
        }

        // Add constraint for status values
        if (!$checkConstraintExists('lines', 'chk_line_status')) {
            DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_status CHECK (status IN ('active', 'inactive', 'suspended', 'maintenance'))");
        }

        // Enhance branches table with validation
        // Ensure branch code follows pattern (2 letters + 3 digits)
        if (!$checkConstraintExists('branches', 'chk_branch_code_format')) {
            DB::statement("ALTER TABLE branches ADD CONSTRAINT chk_branch_code_format CHECK (branch_code REGEXP '^[A-Z]{2}[0-9]{3}$')");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Helper function to check if a constraint exists before trying to drop it
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

        // Remove constraints if they exist
        if ($checkConstraintExists('transactions', 'chk_transaction_amount_positive')) {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_transaction_amount_positive');
        }

        if ($checkConstraintExists('transactions', 'chk_commission_non_negative')) {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_commission_non_negative');
        }

        if ($checkConstraintExists('transactions', 'chk_deduction_non_negative')) {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_deduction_non_negative');
        }

        if ($checkConstraintExists('transactions', 'chk_transaction_type')) {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_transaction_type');
        }

        if ($checkConstraintExists('transactions', 'chk_transaction_status')) {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_transaction_status');
        }

        if ($checkConstraintExists('users', 'chk_user_salary_positive')) {
            DB::statement('ALTER TABLE users DROP CONSTRAINT chk_user_salary_positive');
        }

        if ($checkConstraintExists('users', 'chk_national_number_format')) {
            DB::statement('ALTER TABLE users DROP CONSTRAINT chk_national_number_format');
        }

        if ($checkConstraintExists('customers', 'chk_customer_balance_reasonable')) {
            DB::statement('ALTER TABLE customers DROP CONSTRAINT chk_customer_balance_reasonable');
        }

        if ($checkConstraintExists('customers', 'chk_customer_gender')) {
            DB::statement('ALTER TABLE customers DROP CONSTRAINT chk_customer_gender');
        }

        if ($checkConstraintExists('safes', 'chk_safe_balance_reasonable')) {
            DB::statement('ALTER TABLE safes DROP CONSTRAINT chk_safe_balance_reasonable');
        }

        if ($checkConstraintExists('safes', 'chk_safe_type')) {
            DB::statement('ALTER TABLE safes DROP CONSTRAINT chk_safe_type');
        }

        if ($checkConstraintExists('lines', 'chk_line_limits_positive')) {
            DB::statement('ALTER TABLE `lines` DROP CONSTRAINT chk_line_limits_positive');
        }

        if ($checkConstraintExists('lines', 'chk_line_usage_non_negative')) {
            DB::statement('ALTER TABLE `lines` DROP CONSTRAINT chk_line_usage_non_negative');
        }

        if ($checkConstraintExists('lines', 'chk_line_usage_within_limits')) {
            DB::statement('ALTER TABLE `lines` DROP CONSTRAINT chk_line_usage_within_limits');
        }

        if ($checkConstraintExists('lines', 'chk_line_network')) {
            DB::statement('ALTER TABLE `lines` DROP CONSTRAINT chk_line_network');
        }

        if ($checkConstraintExists('lines', 'chk_line_status')) {
            DB::statement('ALTER TABLE `lines` DROP CONSTRAINT chk_line_status');
        }

        if ($checkConstraintExists('branches', 'chk_branch_code_format')) {
            DB::statement('ALTER TABLE branches DROP CONSTRAINT chk_branch_code_format');
        }
    }
};
