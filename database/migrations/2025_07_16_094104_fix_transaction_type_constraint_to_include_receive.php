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
        // Drop the existing constraint if it exists
        try {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_transaction_type');
        } catch (\Exception $e) {
            // Ignore if constraint does not exist
        }

        // Add the updated constraint with 'Receive' included
        try {
            DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment', 'Receive'))");
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the existing constraint
        try {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_transaction_type');
        } catch (\Exception $e) {
            // Ignore if constraint does not exist
        }

        // Add back the original constraint without 'Receive'
        try {
            DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment'))");
        } catch (\Exception $e) {
        }
    }
};
