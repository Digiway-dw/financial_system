<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        try {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_transaction_type');
        } catch (\Exception $e) {
            // Ignore if constraint does not exist
        }

        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment', 'Receive', 'line_transfer'))");
    }

    public function down(): void
    {
        try {
            DB::statement('ALTER TABLE transactions DROP CONSTRAINT chk_transaction_type');
        } catch (\Exception $e) {
            // Ignore if constraint does not exist
        }

        DB::statement("ALTER TABLE transactions ADD CONSTRAINT chk_transaction_type CHECK (transaction_type IN ('Transfer', 'Withdrawal', 'Deposit', 'Adjustment', 'Receive'))");
    }
};
