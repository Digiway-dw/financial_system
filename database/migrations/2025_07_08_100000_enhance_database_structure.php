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
        // Enhanced indexing for better performance

        // Add composite indexes for frequently queried columns
        Schema::table('transactions', function (Blueprint $table) {
            // Index for status-based queries
            $table->index(['status', 'created_at'], 'idx_transactions_status_created');
            // Index for agent performance queries
            $table->index(['agent_id', 'transaction_date_time'], 'idx_transactions_agent_date');
            // Index for customer transaction history
            $table->index(['customer_mobile_number', 'transaction_date_time'], 'idx_transactions_customer_date');
            // Index for branch-based transaction queries
            $table->index(['safe_id', 'status', 'created_at'], 'idx_transactions_safe_status_created');
            // Index for commission calculations
            $table->index(['transaction_type', 'created_at'], 'idx_transactions_type_created');
        });

        // Enhanced users table indexing
        Schema::table('users', function (Blueprint $table) {
            // Index for branch-based user queries
            $table->index(['branch_id', 'created_at'], 'idx_users_branch_created');
            // Index for user activity tracking
            $table->index(['email_verified_at', 'deleted_at'], 'idx_users_verified_deleted');
            // Index for national number lookups
            $table->index('national_number', 'idx_users_national_number');
        });

        // Enhanced customers table indexing
        Schema::table('customers', function (Blueprint $table) {
            // Index for customer code lookups
            $table->index('customer_code', 'idx_customers_code');
            // Index for gender-based queries
            $table->index(['gender', 'created_at'], 'idx_customers_gender_created');
            // Index for agent-customer relationships
            $table->index(['agent_id', 'branch_id'], 'idx_customers_agent_branch');
            // Index for client status
            $table->index(['is_client', 'created_at'], 'idx_customers_client_created');
        });

        // Enhanced branches table indexing
        Schema::table('branches', function (Blueprint $table) {
            // Index for branch code searches
            $table->index('branch_code', 'idx_branches_code');
            // Index for location-based queries
            $table->index('location', 'idx_branches_location');
        });

        // Enhanced safes table indexing
        Schema::table('safes', function (Blueprint $table) {
            // Index for balance queries
            $table->index(['current_balance', 'branch_id'], 'idx_safes_balance_branch');
            // Index for safe type queries
            $table->index(['type', 'branch_id'], 'idx_safes_type_branch');
        });

        // Enhanced lines table indexing
        Schema::table('lines', function (Blueprint $table) {
            // Index for mobile number searches
            $table->index('mobile_number', 'idx_lines_mobile');
            // Index for network-based queries
            $table->index(['network', 'status'], 'idx_lines_network_status');
            // Index for branch line management
            $table->index(['branch_id', 'status'], 'idx_lines_branch_status');
            // Index for usage tracking
            $table->index(['daily_usage', 'monthly_usage'], 'idx_lines_usage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop composite indexes
        Schema::table('transactions', function (Blueprint $table) {
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE `transactions` DROP FOREIGN KEY `transactions_agent_id_foreign`');
            } catch (\Throwable $e) {
                // Ignore if already dropped or doesn't exist
            }
            $indexes = [
                'idx_transactions_status_created',
                'idx_transactions_agent_date',
                'idx_transactions_customer_date',
                'idx_transactions_safe_status_created',
                'idx_transactions_type_created'
            ];
            foreach ($indexes as $index) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `transactions` DROP INDEX `$index`");
                } catch (\Throwable $e) {
                    // Ignore if already dropped or doesn't exist
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            // Drop the foreign key if it exists before dropping the index
            try {
                \Illuminate\Support\Facades\DB::statement('ALTER TABLE `users` DROP FOREIGN KEY `users_branch_id_foreign`');
            } catch (\Throwable $e) {
                // Ignore if already dropped or doesn't exist
            }
            $indexes = [
                'idx_users_branch_created',
                'idx_users_verified_deleted',
                'idx_users_national_number'
            ];
            foreach ($indexes as $index) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` DROP INDEX `$index`");
                } catch (\Throwable $e) {
                    // Ignore if already dropped or doesn't exist
                }
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            $indexes = [
                'idx_customers_code',
                'idx_customers_gender_created',
                'idx_customers_agent_branch',
                'idx_customers_client_created'
            ];
            foreach ($indexes as $index) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `customers` DROP INDEX `$index`");
                } catch (\Throwable $e) {
                    // Ignore if already dropped or doesn't exist
                }
            }
        });

        Schema::table('branches', function (Blueprint $table) {
            $indexes = [
                'idx_branches_code',
                'idx_branches_location'
            ];
            foreach ($indexes as $index) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `branches` DROP INDEX `$index`");
                } catch (\Throwable $e) {
                    // Ignore if already dropped or doesn't exist
                }
            }
        });

        Schema::table('safes', function (Blueprint $table) {
            $indexes = [
                'idx_safes_balance_branch',
                'idx_safes_type_branch'
            ];
            foreach ($indexes as $index) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `safes` DROP INDEX `$index`");
                } catch (\Throwable $e) {
                    // Ignore if already dropped or doesn't exist
                }
            }
        });

        Schema::table('lines', function (Blueprint $table) {
            $indexes = [
                'idx_lines_mobile',
                'idx_lines_network_status',
                'idx_lines_branch_status',
                'idx_lines_usage'
            ];
            foreach ($indexes as $index) {
                try {
                    \Illuminate\Support\Facades\DB::statement("ALTER TABLE `lines` DROP INDEX `$index`");
                } catch (\Throwable $e) {
                    // Ignore if already dropped or doesn't exist
                }
            }
        });
    }
};
