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

        // Enhance transactions table with better constraints
        if (Schema::hasTable('transactions')) {
            try {
                // Add check constraints for amounts
                DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_transaction_amount_positive CHECK (amount > 0)');
                DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_commission_non_negative CHECK (commission >= 0)');
                DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_deduction_non_negative CHECK (deduction >= 0)');

                // Add constraint for transaction types
                DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment'))");

                // Add constraint for status values
                DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_status CHECK (status IN ('pending', 'completed', 'rejected', 'cancelled'))");
            } catch (\Exception $e) {
                // Log error but continue with migration
                error_log('Error adding constraints to transactions table: ' . $e->getMessage());
            }
        }

        // Enhance users table with better validation
        if (Schema::hasTable('users')) {
            try {
                // Add constraint for salary (must be positive if provided)
                if (Schema::hasColumn('users', 'salary')) {
                    DB::statement('ALTER TABLE users ADD CONSTRAINT chk_user_salary_positive CHECK (salary IS NULL OR salary > 0)');
                }

                // Add constraint for national number format (14 digits)
                if (Schema::hasColumn('users', 'national_number')) {
                    DB::statement("ALTER TABLE users ADD CONSTRAINT chk_national_number_format CHECK (national_number IS NULL OR (LENGTH(national_number) = 14 AND national_number REGEXP '^[0-9]+$'))");
                }
            } catch (\Exception $e) {
                error_log('Error adding constraints to users table: ' . $e->getMessage());
            }
        }

        // Enhance customers table with validation
        if (Schema::hasTable('customers')) {
            try {
                // Add constraint for balance (can be negative for debt tracking)
                if (Schema::hasColumn('customers', 'balance')) {
                    DB::statement('ALTER TABLE customers ADD CONSTRAINT chk_customer_balance_reasonable CHECK (balance >= -1000000 AND balance <= 1000000)');
                }

                // Add constraint for gender values
                if (Schema::hasColumn('customers', 'gender')) {
                    DB::statement("ALTER TABLE customers ADD CONSTRAINT chk_customer_gender CHECK (gender IS NULL OR gender IN ('male', 'female'))");
                }
            } catch (\Exception $e) {
                error_log('Error adding constraints to customers table: ' . $e->getMessage());
            }
        }

        // Enhance safes table with validation
        if (Schema::hasTable('safes')) {
            try {
                // Add constraint for safe balance (should not go extremely negative)
                if (Schema::hasColumn('safes', 'current_balance')) {
                    DB::statement('ALTER TABLE safes ADD CONSTRAINT chk_safe_balance_reasonable CHECK (current_balance >= -10000000)');
                }

                // Add constraint for safe type
                if (Schema::hasColumn('safes', 'type')) {
                    DB::statement("ALTER TABLE safes ADD CONSTRAINT chk_safe_type CHECK (type IN ('branch', 'main', 'temporary', 'reserve'))");
                }
            } catch (\Exception $e) {
                error_log('Error adding constraints to safes table: ' . $e->getMessage());
            }
        }

        // Enhance lines table with validation (quote table name for reserved word)
        if (Schema::hasTable('lines')) {
            try {
                // Add constraints for limits and usage
                if (Schema::hasColumn('lines', 'daily_limit') && Schema::hasColumn('lines', 'monthly_limit')) {
                    DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_limits_positive CHECK (daily_limit > 0 AND monthly_limit > 0)');
                }

                if (Schema::hasColumn('lines', 'daily_usage') && Schema::hasColumn('lines', 'monthly_usage')) {
                    DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_usage_non_negative CHECK (daily_usage >= 0 AND monthly_usage >= 0)');
                }

                if (
                    Schema::hasColumn('lines', 'daily_usage') && Schema::hasColumn('lines', 'monthly_usage') &&
                    Schema::hasColumn('lines', 'daily_limit') && Schema::hasColumn('lines', 'monthly_limit')
                ) {
                    DB::statement('ALTER TABLE `lines` ADD CONSTRAINT chk_line_usage_within_limits CHECK (daily_usage <= daily_limit AND monthly_usage <= monthly_limit)');
                }

                // Add constraint for network values
                if (Schema::hasColumn('lines', 'network')) {
                    DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_network CHECK (network IN ('orange', 'vodafone', 'etisalat', 'we'))");
                }

                // Add constraint for status values
                if (Schema::hasColumn('lines', 'status')) {
                    DB::statement("ALTER TABLE `lines` ADD CONSTRAINT chk_line_status CHECK (status IN ('active', 'inactive', 'suspended', 'maintenance'))");
                }
            } catch (\Exception $e) {
                error_log('Error adding constraints to lines table: ' . $e->getMessage());
            }
        }

        // Enhance branches table with validation
        if (Schema::hasTable('branches')) {
            try {
                // Ensure branch code follows pattern (2 letters + 3 digits)
                if (Schema::hasColumn('branches', 'branch_code')) {
                    DB::statement("ALTER TABLE branches ADD CONSTRAINT chk_branch_code_format CHECK (branch_code REGEXP '^[A-Z]{2}[0-9]{3}$')");
                }
            } catch (\Exception $e) {
                error_log('Error adding constraints to branches table: ' . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove constraints in reverse order
        if (Schema::hasTable('transactions')) {
            try {
                DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_transaction_amount_positive');
                DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_commission_non_negative');
                DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_deduction_non_negative');
                DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_transaction_type');
                DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_transaction_status');
            } catch (\Exception $e) {
                error_log('Error removing constraints from transactions table: ' . $e->getMessage());
            }
        }

        if (Schema::hasTable('users')) {
            try {
                DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS chk_user_salary_positive');
                DB::statement('ALTER TABLE users DROP CONSTRAINT IF EXISTS chk_national_number_format');
            } catch (\Exception $e) {
                error_log('Error removing constraints from users table: ' . $e->getMessage());
            }
        }

        if (Schema::hasTable('customers')) {
            try {
                DB::statement('ALTER TABLE customers DROP CONSTRAINT IF EXISTS chk_customer_balance_reasonable');
                DB::statement('ALTER TABLE customers DROP CONSTRAINT IF EXISTS chk_customer_gender');
            } catch (\Exception $e) {
                error_log('Error removing constraints from customers table: ' . $e->getMessage());
            }
        }

        if (Schema::hasTable('safes')) {
            try {
                DB::statement('ALTER TABLE safes DROP CONSTRAINT IF EXISTS chk_safe_balance_reasonable');
                DB::statement('ALTER TABLE safes DROP CONSTRAINT IF EXISTS chk_safe_type');
            } catch (\Exception $e) {
                error_log('Error removing constraints from safes table: ' . $e->getMessage());
            }
        }

        if (Schema::hasTable('lines')) {
            try {
                DB::statement('ALTER TABLE `lines` DROP CONSTRAINT IF EXISTS chk_line_limits_positive');
                DB::statement('ALTER TABLE `lines` DROP CONSTRAINT IF EXISTS chk_line_usage_non_negative');
                DB::statement('ALTER TABLE `lines` DROP CONSTRAINT IF EXISTS chk_line_usage_within_limits');
                DB::statement('ALTER TABLE `lines` DROP CONSTRAINT IF EXISTS chk_line_network');
                DB::statement('ALTER TABLE `lines` DROP CONSTRAINT IF EXISTS chk_line_status');
            } catch (\Exception $e) {
                error_log('Error removing constraints from lines table: ' . $e->getMessage());
            }
        }

        if (Schema::hasTable('branches')) {
            try {
                DB::statement('ALTER TABLE branches DROP CONSTRAINT IF EXISTS chk_branch_code_format');
            } catch (\Exception $e) {
                error_log('Error removing constraints from branches table: ' . $e->getMessage());
            }
        }
    }
};
