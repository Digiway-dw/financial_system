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
        // The 'type' column already exists due to a previous migration or manual addition.
        // This migration is intentionally left empty to prevent duplicate column errors.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safes', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
