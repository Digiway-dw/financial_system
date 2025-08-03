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
        Schema::table('lines', function (Blueprint $table) {
            $table->date('last_daily_reset')->nullable()->after('current_balance');
            $table->date('last_monthly_reset')->nullable()->after('last_daily_reset');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn(['last_daily_reset', 'last_monthly_reset']);
        });
    }
}; 