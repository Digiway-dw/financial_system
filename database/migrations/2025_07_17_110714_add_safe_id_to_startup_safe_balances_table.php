<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('startup_safe_balances', function (Blueprint $table) {
            // Drop any foreign key constraints on branch_id if they exist
            try {
                $table->dropForeign(['branch_id']);
            } catch (\Exception $e) {}
            // Remove old unique
            $table->dropUnique('startup_safe_balances_branch_id_date_unique');
            // Add foreign key and new unique constraint
            $table->foreign('safe_id')->references('id')->on('safes')->onDelete('cascade');
            $table->unique(['safe_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::table('startup_safe_balances', function (Blueprint $table) {
            $table->dropForeign(['safe_id']);
            $table->dropUnique(['safe_id', 'date']);
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->unique(['branch_id', 'date']);
        });
    }
};
