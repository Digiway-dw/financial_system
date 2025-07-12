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
        // SQLite-compatible version of the constraints
        // SQLite doesn't support ADD CONSTRAINT syntax like MySQL, 
        // so we'll skip it during testing with SQLite

        if (DB::connection()->getDriverName() !== 'sqlite') {
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
            if (!$checkConstraintExists('transactions', 'chk_transaction_amount_positive')) {
                DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_transaction_amount_positive CHECK (amount > 0)');
            }

            if (!$checkConstraintExists('transactions', 'chk_commission_non_negative')) {
                DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_commission_non_negative CHECK (commission >= 0)');
            }

            if (!$checkConstraintExists('transactions', 'chk_deduction_non_negative')) {
                DB::statement('ALTER TABLE transactions ADD CONSTRAINT chk_deduction_non_negative CHECK (deduction >= 0)');
            }

            // Add constraint for transaction types
            if (!$checkConstraintExists('transactions', 'chk_transaction_type')) {
                DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment'))");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() !== 'sqlite') {
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

            // Drop the constraints if they exist
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
        }
    }
};
