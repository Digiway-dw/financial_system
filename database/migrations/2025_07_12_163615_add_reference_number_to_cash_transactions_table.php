<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('cash_transactions', 'reference_number')) {
            Schema::table('cash_transactions', function (Blueprint $table) {
                $table->string('reference_number')->unique()->nullable()->after('customer_code');
            });
        }
    }

    public function down()
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            $table->dropColumn('reference_number');
        });
    }
};
