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
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'receiver_mobile_number')) {
                $table->string('receiver_mobile_number', 20)->nullable()->after('customer_mobile_number');
            }
            if (!Schema::hasColumn('transactions', 'discount_notes')) {
                $table->text('discount_notes')->nullable()->after('deduction');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'receiver_mobile_number')) {
                $table->dropColumn('receiver_mobile_number');
            }
            if (Schema::hasColumn('transactions', 'discount_notes')) {
                $table->dropColumn('discount_notes');
            }
        });
    }
};
