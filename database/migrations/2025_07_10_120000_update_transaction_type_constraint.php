<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop the existing constraint
        DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_transaction_type');

        // Add the updated constraint with 'Receive' included
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment', 'Receive'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop the updated constraint
        DB::statement('ALTER TABLE transactions DROP CONSTRAINT IF EXISTS chk_transaction_type');

        // Restore the original constraint
        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment'))");
    }
};
