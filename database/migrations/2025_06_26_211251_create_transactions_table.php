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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_mobile_number');
            $table->foreignId('line_id')->nullable()->constrained('lines')->onDelete('set null');
            $table->string('customer_code')->nullable();
            $table->decimal('amount', 15, 2);
            $table->decimal('commission', 15, 2);
            $table->decimal('deduction', 15, 2)->default(0.00);
            $table->string('transaction_type'); // Transfer / Withdrawal / Deposit / Adjustment
            $table->foreignId('agent_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('transaction_date_time')->useCurrent();
            $table->string('status')->default('pending'); // Completed / Pending / Rejected
            $table->foreignId('safe_id')->nullable()->constrained('safes')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
