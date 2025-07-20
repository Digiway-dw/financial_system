<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('working_hours', function (Blueprint $table) {
            $table->boolean('is_enabled')->default(true)->after('end_time');
        });
    }

    public function down()
    {
        Schema::table('working_hours', function (Blueprint $table) {
            $table->dropColumn('is_enabled');
        });
    }
}; 