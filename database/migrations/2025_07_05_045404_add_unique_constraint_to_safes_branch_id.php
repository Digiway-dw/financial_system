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
        Schema::table('safes', function (Blueprint $table) {
            $table->unique('branch_id', 'safes_branch_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('safes', function (Blueprint $table) {
            // Drop the foreign key first if it exists
            try {
                $table->dropForeign(['branch_id']);
            } catch (\Exception $e) {}
            $table->dropUnique('safes_branch_id_unique');
        });
    }
};
