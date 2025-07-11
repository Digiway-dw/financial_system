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
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->decimal('amount', 15, 2);
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('safe_id')->nullable();
            $table->enum('transaction_type', ['Deposit', 'Withdrawal']);
            $table->string('status')->default('Completed');
            $table->timestamp('transaction_date_time')->useCurrent();
            $table->string('depositor_national_id', 14)->nullable();
            $table->string('depositor_mobile_number', 15)->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->string('customer_code')->nullable();
            $table->timestamps();
            // Future fields can be added here as needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
    }
};
