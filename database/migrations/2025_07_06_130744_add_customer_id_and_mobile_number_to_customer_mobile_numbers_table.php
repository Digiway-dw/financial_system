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
        Schema::table('customer_mobile_numbers', function (Blueprint $table) {
            if (!Schema::hasColumn('customer_mobile_numbers', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable();
            }
            if (!Schema::hasColumn('customer_mobile_numbers', 'mobile_number')) {
                $table->string('mobile_number')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_mobile_numbers', function (Blueprint $table) {
            if (Schema::hasColumn('customer_mobile_numbers', 'customer_id')) {
                $table->dropColumn('customer_id');
            }
            if (Schema::hasColumn('customer_mobile_numbers', 'mobile_number')) {
                $table->dropColumn('mobile_number');
            }
        });
    }
};
