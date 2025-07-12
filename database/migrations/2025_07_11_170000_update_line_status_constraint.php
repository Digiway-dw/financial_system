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
            // Change the status column to allow 'frozen' as a value
            $table->enum('status', ['active', 'inactive', 'blocked', 'frozen'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            // Revert to previous allowed values (without 'frozen')
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active')->change();
        });
    }
}; 