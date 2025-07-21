<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Convert all money-related decimal fields to integers (no decimals allowed).
     */
    public function up(): void
    {
        // Convert transactions table money fields (if table and columns exist)
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'amount')) {
                    DB::statement('UPDATE transactions SET amount = ROUND(amount * 100)');
                    $table->bigInteger('amount')->change();
                }
                if (Schema::hasColumn('transactions', 'commission')) {
                    DB::statement('UPDATE transactions SET commission = ROUND(commission * 100) WHERE commission IS NOT NULL');
                    $table->bigInteger('commission')->nullable()->change();
                }
                if (Schema::hasColumn('transactions', 'deduction')) {
                    DB::statement('UPDATE transactions SET deduction = ROUND(deduction * 100)');
                    $table->bigInteger('deduction')->default(0)->change();
                }
            });
        }

        // Convert safes table balance field (if table and column exist)
        if (Schema::hasTable('safes') && Schema::hasColumn('safes', 'balance')) {
            Schema::table('safes', function (Blueprint $table) {
                DB::statement('UPDATE safes SET balance = ROUND(balance * 100)');
                $table->bigInteger('balance')->default(0)->change();
            });
        }

        // Convert customers table balance field (if table and column exist)
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'balance')) {
            Schema::table('customers', function (Blueprint $table) {
                DB::statement('UPDATE customers SET balance = ROUND(balance * 100)');
                $table->bigInteger('balance')->default(0)->change();
            });
        }

        // Convert startup_safe_balances table balance field (if table and column exist)
        if (Schema::hasTable('startup_safe_balances') && Schema::hasColumn('startup_safe_balances', 'balance')) {
            Schema::table('startup_safe_balances', function (Blueprint $table) {
                DB::statement('UPDATE startup_safe_balances SET balance = ROUND(balance * 100)');
                $table->bigInteger('balance')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     * Convert back to decimal fields (for rollback purposes).
     */
    public function down(): void
    {
        // Revert transactions table (if table and columns exist)
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'amount')) {
                    DB::statement('UPDATE transactions SET amount = amount / 100');
                    $table->decimal('amount', 15, 2)->change();
                }
                if (Schema::hasColumn('transactions', 'commission')) {
                    DB::statement('UPDATE transactions SET commission = commission / 100 WHERE commission IS NOT NULL');
                    $table->decimal('commission', 15, 2)->nullable()->change();
                }
                if (Schema::hasColumn('transactions', 'deduction')) {
                    DB::statement('UPDATE transactions SET deduction = deduction / 100');
                    $table->decimal('deduction', 15, 2)->default(0.00)->change();
                }
            });
        }

        // Revert safes table (if table and column exist)
        if (Schema::hasTable('safes') && Schema::hasColumn('safes', 'balance')) {
            Schema::table('safes', function (Blueprint $table) {
                DB::statement('UPDATE safes SET balance = balance / 100');
                $table->decimal('balance', 15, 2)->default(0.00)->change();
            });
        }

        // Revert customers table (if table and column exist)
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'balance')) {
            Schema::table('customers', function (Blueprint $table) {
                DB::statement('UPDATE customers SET balance = balance / 100');
                $table->decimal('balance', 10, 2)->default(0.00)->change();
            });
        }

        // Revert startup_safe_balances table (if table and column exist)
        if (Schema::hasTable('startup_safe_balances') && Schema::hasColumn('startup_safe_balances', 'balance')) {
            Schema::table('startup_safe_balances', function (Blueprint $table) {
                DB::statement('UPDATE startup_safe_balances SET balance = balance / 100');
                $table->decimal('balance', 15, 2)->change();
            });
        }
    }
};
