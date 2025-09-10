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
            // Add fields for line transfer functionality
            $table->unsignedBigInteger('from_line_id')->nullable()->after('line_id');
            $table->unsignedBigInteger('to_line_id')->nullable()->after('from_line_id');
            $table->decimal('extra_fee', 10, 2)->default(0)->after('deduction');
            $table->decimal('total_deducted', 10, 2)->nullable()->after('extra_fee');
            
            // Add foreign key constraints
            $table->foreign('from_line_id')->references('id')->on('lines')->onDelete('set null');
            $table->foreign('to_line_id')->references('id')->on('lines')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['from_line_id']);
            $table->dropForeign(['to_line_id']);
            
            // Drop columns
            $table->dropColumn(['from_line_id', 'to_line_id', 'extra_fee', 'total_deducted']);
        });
    }
};
