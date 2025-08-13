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
        Schema::create('safes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('balance', 15, 2)->default(0.00);
            $table->foreignId('branch_id')->constrained()->onDelete('cascade');
            $table->string('safe_number')->nullable(); // Added for reporting indexes
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop dependent tables first to avoid foreign key constraint errors
        Schema::dropIfExists('daily_safe_balances');
        Schema::dropIfExists('safes');
    }
};
