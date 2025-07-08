<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * This migration optimizes and cleans up existing database structure
     */
    public function up(): void
    {
        // Fix any inconsistencies in existing tables
        $this->fixDataInconsistencies();
        
        // Add missing foreign key constraints
        $this->addMissingForeignKeys();
        
        // Optimize table storage engines and collations
        $this->optimizeTableStorage();
        
        // Create additional indexes for better performance
        $this->addPerformanceIndexes();
    }

    /**
     * Fix data inconsistencies in existing tables
     */
    private function fixDataInconsistencies(): void
    {
        // Ensure all transactions have valid agent_id
        DB::statement("
            UPDATE transactions 
            SET status = 'cancelled' 
            WHERE agent_id NOT IN (SELECT id FROM users WHERE deleted_at IS NULL)
            AND status = 'pending'
        ");

        // Ensure all users have valid branch_id (set to null if branch doesn't exist)
        DB::statement("
            UPDATE users 
            SET branch_id = NULL 
            WHERE branch_id IS NOT NULL 
            AND branch_id NOT IN (SELECT id FROM branches)
        ");

        // Ensure all safes have valid branch_id
        DB::statement("
            DELETE FROM safes 
            WHERE branch_id NOT IN (SELECT id FROM branches)
        ");

        // Fix negative balances in safes where inappropriate
        DB::statement("
            UPDATE safes 
            SET current_balance = 0 
            WHERE current_balance < -1000000
        ");

        // Update customer mobile numbers to ensure consistency
        DB::statement("
            UPDATE customers 
            SET mobile_number = TRIM(mobile_number)
            WHERE mobile_number != TRIM(mobile_number)
        ");
    }

    /**
     * Add missing foreign key constraints
     */
    private function addMissingForeignKeys(): void
    {
        // Add foreign key for transaction approvals (only if column exists)
        if (Schema::hasColumn('transactions', 'approved_by') && 
            !$this->foreignKeyExists('transactions', 'fk_transactions_approved_by')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('approved_by', 'fk_transactions_approved_by')
                      ->references('id')->on('users')
                      ->onDelete('set null');
            });
        }

        // Add foreign key for transaction reviewers (only if column exists)
        if (Schema::hasColumn('transactions', 'reviewer_id') && 
            !$this->foreignKeyExists('transactions', 'fk_transactions_reviewer_id')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->foreign('reviewer_id', 'fk_transactions_reviewer_id')
                      ->references('id')->on('users')
                      ->onDelete('set null');
            });
        }

        // Add foreign key for customer agent relationship (only if column exists)
        if (Schema::hasColumn('customers', 'agent_id') && 
            !$this->foreignKeyExists('customers', 'fk_customers_agent_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreign('agent_id', 'fk_customers_agent_id')
                      ->references('id')->on('users')
                      ->onDelete('set null');
            });
        }

        // Add foreign key for customer branch relationship
        if (!$this->foreignKeyExists('customers', 'fk_customers_branch_id')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->foreign('branch_id', 'fk_customers_branch_id')
                      ->references('id')->on('branches')
                      ->onDelete('set null');
            });
        }
    }

    /**
     * Optimize table storage engines and collations
     */
    private function optimizeTableStorage(): void
    {
        $tables = [
            'users', 'branches', 'transactions', 'customers', 'safes', 'lines',
            'activity_log', 'login_histories', 'notifications', 'personal_access_tokens'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                // Quote table name for reserved words like 'lines'
                $quotedTable = "`{$table}`";
                DB::statement("ALTER TABLE {$quotedTable} ENGINE=InnoDB");
                DB::statement("ALTER TABLE {$quotedTable} CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            }
        }
    }

    /**
     * Add performance indexes
     */
    private function addPerformanceIndexes(): void
    {
        // Customer mobile numbers index for faster transaction lookups
        if (!$this->indexExists('customer_mobile_numbers', 'idx_customer_mobile_numbers_lookup')) {
            Schema::table('customer_mobile_numbers', function (Blueprint $table) {
                $table->index(['mobile_number', 'customer_id'], 'idx_customer_mobile_numbers_lookup');
            });
        }

        // Activity log indexes for audit queries
        if (Schema::hasTable('activity_log') && !$this->indexExists('activity_log', 'idx_activity_log_subject_date')) {
            Schema::table('activity_log', function (Blueprint $table) {
                $table->index(['subject_type', 'subject_id', 'created_at'], 'idx_activity_log_subject_date');
                $table->index(['causer_type', 'causer_id', 'created_at'], 'idx_activity_log_causer_date');
            });
        }

        // Notifications index for user queries
        if (Schema::hasTable('notifications') && !$this->indexExists('notifications', 'idx_notifications_user_read')) {
            Schema::table('notifications', function (Blueprint $table) {
                $table->index(['notifiable_type', 'notifiable_id', 'read_at'], 'idx_notifications_user_read');
            });
        }

        // Personal access tokens index
        if (Schema::hasTable('personal_access_tokens') && !$this->indexExists('personal_access_tokens', 'idx_personal_access_tokens_tokenable')) {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->index(['tokenable_type', 'tokenable_id'], 'idx_personal_access_tokens_tokenable');
            });
        }
    }

    /**
     * Check if foreign key exists
     */
    private function foreignKeyExists(string $table, string $keyName): bool
    {
        $result = DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND CONSTRAINT_NAME = '{$keyName}'
        ");

        return count($result) > 0;
    }

    /**
     * Check if index exists
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $result = DB::select("
            SELECT INDEX_NAME 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '{$table}' 
            AND INDEX_NAME = '{$indexName}'
        ");

        return count($result) > 0;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove added foreign keys (only if they exist)
        Schema::table('transactions', function (Blueprint $table) {
            if ($this->foreignKeyExists('transactions', 'fk_transactions_approved_by')) {
                $table->dropForeign('fk_transactions_approved_by');
            }
            if ($this->foreignKeyExists('transactions', 'fk_transactions_reviewer_id')) {
                $table->dropForeign('fk_transactions_reviewer_id');
            }
        });

        Schema::table('customers', function (Blueprint $table) {
            if ($this->foreignKeyExists('customers', 'fk_customers_agent_id')) {
                $table->dropForeign('fk_customers_agent_id');
            }
            if ($this->foreignKeyExists('customers', 'fk_customers_branch_id')) {
                $table->dropForeign('fk_customers_branch_id');
            }
        });

        // Remove added indexes
        if ($this->indexExists('customer_mobile_numbers', 'idx_customer_mobile_numbers_lookup')) {
            Schema::table('customer_mobile_numbers', function (Blueprint $table) {
                $table->dropIndex('idx_customer_mobile_numbers_lookup');
            });
        }

        if (Schema::hasTable('activity_log')) {
            if ($this->indexExists('activity_log', 'idx_activity_log_subject_date')) {
                Schema::table('activity_log', function (Blueprint $table) {
                    $table->dropIndex('idx_activity_log_subject_date');
                    $table->dropIndex('idx_activity_log_causer_date');
                });
            }
        }

        if (Schema::hasTable('notifications')) {
            if ($this->indexExists('notifications', 'idx_notifications_user_read')) {
                Schema::table('notifications', function (Blueprint $table) {
                    $table->dropIndex('idx_notifications_user_read');
                });
            }
        }

        if (Schema::hasTable('personal_access_tokens')) {
            if ($this->indexExists('personal_access_tokens', 'idx_personal_access_tokens_tokenable')) {
                Schema::table('personal_access_tokens', function (Blueprint $table) {
                    $table->dropIndex('idx_personal_access_tokens_tokenable');
                });
            }
        }
    }
};
