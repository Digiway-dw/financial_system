<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->decimal('daily_remaining', 15, 2)->default(0);
            $table->decimal('monthly_remaining', 15, 2)->default(0);
        });
    }

    public function down()
    {
        Schema::table('lines', function (Blueprint $table) {
            $table->dropColumn('daily_remaining');
            $table->dropColumn('monthly_remaining');
        });
    }
}; 