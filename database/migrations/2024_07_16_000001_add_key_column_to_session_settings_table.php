<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('session_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('session_settings', 'key')) {
                $table->string('key')->unique()->after('id');
            }
        });
    }

    public function down()
    {
        Schema::table('session_settings', function (Blueprint $table) {
            if (Schema::hasColumn('session_settings', 'key')) {
                $table->dropColumn('key');
            }
        });
    }
}; 