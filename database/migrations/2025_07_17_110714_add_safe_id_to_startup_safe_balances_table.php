<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('startup_safe_balances', function (Blueprint $table) {
            // Add safe_id column if it doesn't exist
            if (!Schema::hasColumn('startup_safe_balances', 'safe_id')) {
                $table->unsignedBigInteger('safe_id')->nullable()->after('branch_id');
            }
            
            // Add foreign key constraint for safe_id
            $table->foreign('safe_id')->references('id')->on('safes')->onDelete('cascade');
            
            // Add unique constraint on safe_id and date
            $table->unique(['safe_id', 'date'], 'startup_safe_balances_safe_id_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('startup_safe_balances', function (Blueprint $table) {
            // Drop the constraints
            $table->dropForeign(['safe_id']);
            $table->dropUnique('startup_safe_balances_safe_id_date_unique');
            
            // Drop the safe_id column
            $table->dropColumn('safe_id');
        });
    }
};
