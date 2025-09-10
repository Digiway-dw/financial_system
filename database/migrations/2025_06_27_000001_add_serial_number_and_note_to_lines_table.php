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
            if (!Schema::hasColumn('lines', 'serial_number')) {
                $table->string('serial_number')->nullable()->after('mobile_number');
            }
            if (!Schema::hasColumn('lines', 'note')) {
                $table->text('note')->nullable()->after('network');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn(['serial_number', 'note']);
        });
    }
};
