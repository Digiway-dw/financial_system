<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('cash_transactions', 'destination_branch_id')) {
                $table->unsignedBigInteger('destination_branch_id')->nullable()->after('agent_id');
            }
            if (!Schema::hasColumn('cash_transactions', 'destination_safe_id')) {
                $table->unsignedBigInteger('destination_safe_id')->nullable()->after('destination_branch_id');
            }
        });
    }

    public function down()
    {
        Schema::table('cash_transactions', function (Blueprint $table) {
            if (Schema::hasColumn('cash_transactions', 'destination_safe_id')) {
                $table->dropColumn('destination_safe_id');
            }
            if (Schema::hasColumn('cash_transactions', 'destination_branch_id')) {
                $table->dropColumn('destination_branch_id');
            }
        });
    }
}; 