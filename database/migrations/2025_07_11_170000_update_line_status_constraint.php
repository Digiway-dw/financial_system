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
            // Change the status column to allow all valid values including 'frozen', 'suspended', and 'maintenance'
            $table->enum('status', ['active', 'inactive', 'suspended', 'maintenance', 'frozen'])->default('active')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            // Revert to previous allowed values (without 'frozen', 'suspended', 'maintenance')
            $table->enum('status', ['active', 'inactive'])->default('active')->change();
        });
    }
}; 