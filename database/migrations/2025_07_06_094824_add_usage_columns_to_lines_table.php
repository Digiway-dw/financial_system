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
            $table->decimal('daily_usage', 15, 2)->default(0.00)->after('monthly_limit');
            $table->decimal('monthly_usage', 15, 2)->default(0.00)->after('daily_usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn(['daily_usage', 'monthly_usage']);
        });
    }
};
