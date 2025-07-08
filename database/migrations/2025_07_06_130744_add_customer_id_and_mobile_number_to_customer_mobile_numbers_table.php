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
        // Skip this migration if the table doesn't exist yet
        if (!Schema::hasTable('customer_mobile_numbers')) {
            return;
        }

        // Check if columns exist before trying to add them
        $hasCustomerId = Schema::hasColumn('customer_mobile_numbers', 'customer_id');
        $hasMobileNumber = Schema::hasColumn('customer_mobile_numbers', 'mobile_number');

        // Only proceed if one of the columns is missing
        if (!$hasCustomerId || !$hasMobileNumber) {
            Schema::table('customer_mobile_numbers', function (Blueprint $table) use ($hasCustomerId, $hasMobileNumber) {
                if (!$hasCustomerId) {
                    $table->unsignedBigInteger('customer_id')->nullable();
                }
                if (!$hasMobileNumber) {
                    $table->string('mobile_number')->nullable();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No need to drop these columns as they are part of the original table structure
    }
};
