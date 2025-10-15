<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing constraint
        try {
            DB::statement('ALTER TABLE transactions DROP CHECK chk_transaction_type');
        } catch (\Exception $e) {
            // Ignore if constraint does not exist
        }

        // Check if constraint already exists
        $constraintExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'transactions' 
            AND CONSTRAINT_NAME = 'chk_transaction_type'
        ");

        // Add the updated constraint with 'Receive' included only if it doesn't exist
        if (empty($constraintExists)) {
            try {
                DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment', 'Receive'))");
            } catch (\Exception $e) {
                // Log the error but don't fail the migration
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the existing constraint
        try {
            DB::statement('ALTER TABLE transactions DROP CHECK chk_transaction_type');
        } catch (\Exception $e) {
            // Ignore if constraint does not exist
        }

        // Before restoring the original constraint, update any 'Receive' transactions to 'Deposit'
        try {
            DB::statement("UPDATE transactions SET transaction_type = 'Deposit' WHERE transaction_type = 'Receive'");
        } catch (\Exception $e) {
            // Ignore if update fails (e.g., no records)
        }

        // Check if constraint already exists before adding
        $constraintExists = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM information_schema.TABLE_CONSTRAINTS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'transactions' 
            AND CONSTRAINT_NAME = 'chk_transaction_type'
        ");

        // Restore the original constraint only if it doesn't exist
        if (empty($constraintExists)) {
            try {
                DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment'))");
            } catch (\Exception $e) {
                // Ignore if constraint addition fails
            }
        }
    }
};
