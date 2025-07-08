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
        if (Schema::hasTable('transactions')) {
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
        }

        // Enhanced users table indexing
        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                // Index for branch-based user queries
                $table->index(['branch_id', 'created_at'], 'idx_users_branch_created');
                // Index for user activity tracking
                $table->index(['email_verified_at', 'deleted_at'], 'idx_users_verified_deleted');
                // Index for national number lookups
                if (Schema::hasColumn('users', 'national_number')) {
                    $table->index('national_number', 'idx_users_national_number');
                }
            });
        }

        // Enhanced customers table indexing
        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                // Index for customer code lookups
                if (Schema::hasColumn('customers', 'customer_code')) {
                    $table->index('customer_code', 'idx_customers_code');
                }
                // Index for gender-based queries
                if (Schema::hasColumn('customers', 'gender')) {
                    $table->index(['gender', 'created_at'], 'idx_customers_gender_created');
                }
                // Index for agent-customer relationships
                if (Schema::hasColumn('customers', 'agent_id') && Schema::hasColumn('customers', 'branch_id')) {
                    $table->index(['agent_id', 'branch_id'], 'idx_customers_agent_branch');
                }
                // Index for client status
                if (Schema::hasColumn('customers', 'is_client')) {
                    $table->index(['is_client', 'created_at'], 'idx_customers_client_created');
                }
            });
        }

        // Enhanced branches table indexing
        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                // Index for branch code searches
                if (Schema::hasColumn('branches', 'branch_code')) {
                    $table->index('branch_code', 'idx_branches_code');
                }
                // Index for location-based queries
                if (Schema::hasColumn('branches', 'location')) {
                    $table->index('location', 'idx_branches_location');
                }
            });
        }

        // Enhanced safes table indexing
        if (Schema::hasTable('safes')) {
            Schema::table('safes', function (Blueprint $table) {
                // Index for balance queries
                if (Schema::hasColumn('safes', 'current_balance') && Schema::hasColumn('safes', 'branch_id')) {
                    $table->index(['current_balance', 'branch_id'], 'idx_safes_balance_branch');
                }
                // Index for safe type queries
                if (Schema::hasColumn('safes', 'type') && Schema::hasColumn('safes', 'branch_id')) {
                    $table->index(['type', 'branch_id'], 'idx_safes_type_branch');
                }
            });
        }

        // Enhanced lines table indexing
        if (Schema::hasTable('lines')) {
            Schema::table('lines', function (Blueprint $table) {
                // Index for mobile number searches
                if (Schema::hasColumn('lines', 'mobile_number')) {
                    $table->index('mobile_number', 'idx_lines_mobile');
                }
                // Index for network-based queries
                if (Schema::hasColumn('lines', 'network') && Schema::hasColumn('lines', 'status')) {
                    $table->index(['network', 'status'], 'idx_lines_network_status');
                }
                // Index for branch line management
                if (Schema::hasColumn('lines', 'branch_id') && Schema::hasColumn('lines', 'status')) {
                    $table->index(['branch_id', 'status'], 'idx_lines_branch_status');
                }
                // Index for usage tracking
                if (Schema::hasColumn('lines', 'daily_usage') && Schema::hasColumn('lines', 'monthly_usage')) {
                    $table->index(['daily_usage', 'monthly_usage'], 'idx_lines_usage');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop composite indexes
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->dropIndex('idx_transactions_status_created');
                $table->dropIndex('idx_transactions_agent_date');
                $table->dropIndex('idx_transactions_customer_date');
                $table->dropIndex('idx_transactions_safe_status_created');
                $table->dropIndex('idx_transactions_type_created');
            });
        }

        if (Schema::hasTable('users')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropIndex('idx_users_branch_created');
                $table->dropIndex('idx_users_verified_deleted');
                if (Schema::hasColumn('users', 'national_number')) {
                    $table->dropIndex('idx_users_national_number');
                }
            });
        }

        if (Schema::hasTable('customers')) {
            Schema::table('customers', function (Blueprint $table) {
                if (Schema::hasColumn('customers', 'customer_code')) {
                    $table->dropIndex('idx_customers_code');
                }
                if (Schema::hasColumn('customers', 'gender')) {
                    $table->dropIndex('idx_customers_gender_created');
                }
                if (Schema::hasColumn('customers', 'agent_id') && Schema::hasColumn('customers', 'branch_id')) {
                    $table->dropIndex('idx_customers_agent_branch');
                }
                if (Schema::hasColumn('customers', 'is_client')) {
                    $table->dropIndex('idx_customers_client_created');
                }
            });
        }

        if (Schema::hasTable('branches')) {
            Schema::table('branches', function (Blueprint $table) {
                if (Schema::hasColumn('branches', 'branch_code')) {
                    $table->dropIndex('idx_branches_code');
                }
                if (Schema::hasColumn('branches', 'location')) {
                    $table->dropIndex('idx_branches_location');
                }
            });
        }

        if (Schema::hasTable('safes')) {
            Schema::table('safes', function (Blueprint $table) {
                if (Schema::hasColumn('safes', 'current_balance') && Schema::hasColumn('safes', 'branch_id')) {
                    $table->dropIndex('idx_safes_balance_branch');
                }
                if (Schema::hasColumn('safes', 'type') && Schema::hasColumn('safes', 'branch_id')) {
                    $table->dropIndex('idx_safes_type_branch');
                }
            });
        }

        if (Schema::hasTable('lines')) {
            Schema::table('lines', function (Blueprint $table) {
                if (Schema::hasColumn('lines', 'mobile_number')) {
                    $table->dropIndex('idx_lines_mobile');
                }
                if (Schema::hasColumn('lines', 'network') && Schema::hasColumn('lines', 'status')) {
                    $table->dropIndex('idx_lines_network_status');
                }
                if (Schema::hasColumn('lines', 'branch_id') && Schema::hasColumn('lines', 'status')) {
                    $table->dropIndex('idx_lines_branch_status');
                }
                if (Schema::hasColumn('lines', 'daily_usage') && Schema::hasColumn('lines', 'monthly_usage')) {
                    $table->dropIndex('idx_lines_usage');
                }
            });
        }
    }
};
