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
        // Drop all triggers related to transactions that could cause issues
        DB::statement('DROP TRIGGER IF EXISTS tr_update_customer_stats_after_transaction_insert');
        DB::statement('DROP TRIGGER IF EXISTS tr_update_customer_stats_after_transaction_update');
        DB::statement('DROP TRIGGER IF EXISTS tr_update_user_last_activity');
        DB::statement('DROP TRIGGER IF EXISTS tr_transaction_audit_insert');
        DB::statement('DROP TRIGGER IF EXISTS tr_transaction_audit_update');
        DB::statement('DROP TRIGGER IF EXISTS tr_generate_transaction_reference');
        DB::statement('DROP TRIGGER IF EXISTS tr_validate_transaction_amounts');
        DB::statement('DROP TRIGGER IF EXISTS tr_update_safe_balance_after_transaction');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for down
    }
}; 