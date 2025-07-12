<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_transactions', 'depositor_national_id')) {
                $table->string('depositor_national_id', 14)->nullable()->after('transaction_date_time');
            }
            if (!Schema::hasColumn('cash_transactions', 'depositor_mobile_number')) {
                $table->string('depositor_mobile_number', 15)->nullable()->after('depositor_national_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('cash_transactions', 'depositor_mobile_number')) {
                $table->dropColumn('depositor_mobile_number');
            }
            if (Schema::hasColumn('cash_transactions', 'depositor_national_id')) {
                $table->dropColumn('depositor_national_id');
            }
        });
    }
}; 