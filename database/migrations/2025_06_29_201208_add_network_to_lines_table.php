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
        // The 'network' column seems to already exist, so this migration will be empty.
        // Schema::table('lines', function (Blueprint $table) {
        //     $table->string('network')->nullable()->after('monthly_limit');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn('network');
        });
    }
};
