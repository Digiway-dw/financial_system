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
        // Add indexes to transactions table for enhanced reporting performance
        Schema::table('transactions', function (Blueprint $table) {
            // Check if indexes don't exist before creating them
            try {
                $table->index('customer_mobile_number', 'idx_transactions_customer_mobile');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index('receiver_mobile_number', 'idx_transactions_receiver_mobile');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index('reference_number', 'idx_transactions_reference');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index('amount', 'idx_transactions_amount');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['transaction_date_time', 'branch_id'], 'idx_transactions_date_branch_new');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['status', 'transaction_date_time'], 'idx_transactions_status_date_new');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });

        // Add indexes to cash_transactions table
        Schema::table('cash_transactions', function (Blueprint $table) {
            try {
                $table->index('depositor_mobile_number', 'idx_cash_transactions_mobile');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index('reference_number', 'idx_cash_transactions_reference');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index('amount', 'idx_cash_transactions_amount');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['transaction_date', 'branch_id'], 'idx_cash_transactions_date_branch');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index(['customer_name', 'transaction_date', 'branch_id'], 'idx_cash_transactions_expense');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });

        // Add indexes to related tables for better join performance
        Schema::table('safes', function (Blueprint $table) {
            try {
                $table->index(['branch_id', 'safe_number'], 'idx_safes_branch_number');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            try {
                $table->index('mobile_number', 'idx_customers_mobile');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index('customer_code', 'idx_customers_code');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });

        Schema::table('users', function (Blueprint $table) {
            try {
                $table->index(['branch_id', 'name'], 'idx_users_branch_name');
            } catch (\Exception $e) {
                // Index might already exist
            }

            try {
                $table->index('employment_start_date', 'idx_users_employment_start');
            } catch (\Exception $e) {
                // Index might already exist
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_transactions_customer_mobile');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_transactions_receiver_mobile');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_transactions_reference');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_transactions_amount');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_transactions_date_branch_new');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_transactions_status_date_new');
            } catch (\Exception $e) {
            }
        });

        Schema::table('cash_transactions', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_cash_transactions_mobile');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_cash_transactions_reference');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_cash_transactions_amount');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_cash_transactions_date_branch');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_cash_transactions_expense');
            } catch (\Exception $e) {
            }
        });

        Schema::table('safes', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_safes_branch_number');
            } catch (\Exception $e) {
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_customers_mobile');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_customers_code');
            } catch (\Exception $e) {
            }
        });

        Schema::table('users', function (Blueprint $table) {
            try {
                $table->dropIndex('idx_users_branch_name');
            } catch (\Exception $e) {
            }
            try {
                $table->dropIndex('idx_users_employment_start');
            } catch (\Exception $e) {
            }
        });
    }
};
