<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Create views for common queries to improve performance

        // Create view for active transactions with user and branch information
        DB::statement("DROP VIEW IF EXISTS v_active_transactions");
        $sql = "CREATE VIEW v_active_transactions AS\n"
            . "SELECT \n"
            . "    t.id,\n"
            . "    t.customer_name,\n"
            . "    t.customer_mobile_number,\n"
            . "    t.amount,\n"
            . "    t.commission,\n"
            . "    t.deduction,\n"
            . "    t.transaction_type,\n"
            . "    t.status,\n"
            . "    t.transaction_date_time,\n"
            . "    COALESCE(t.payment_method, 'cash') as payment_method,\n"
            . "    u.name as agent_name,\n"
            . "    u.email as agent_email,\n"
            . "    b.name as branch_name,\n"
            . "    b.branch_code,\n"
            . "    s.name as safe_name,\n"
            . "    COALESCE(l.mobile_number, '') as line_mobile,\n"
            . "    COALESCE(l.network, '') as line_network\n"
            . "FROM transactions t\n"
            . "LEFT JOIN users u ON t.agent_id = u.id\n"
            . "LEFT JOIN branches b ON u.branch_id = b.id\n"
            . "LEFT JOIN safes s ON t.safe_id = s.id\n"
            . "LEFT JOIN `lines` l ON t.line_id = l.id;";
        DB::statement(trim($sql));

        // Create view for line utilization
        DB::statement("DROP VIEW IF EXISTS v_line_utilization");
        DB::statement(<<<SQL
            CREATE VIEW v_line_utilization AS
            SELECT 
                l.id as line_id,
                l.mobile_number,
                l.network,
                l.status,
                l.current_balance,
                l.daily_limit,
                l.monthly_limit,
                l.daily_starting_balance,
                l.starting_balance,
                GREATEST(0, l.current_balance - l.daily_starting_balance) as daily_usage,
                GREATEST(0, l.current_balance - l.starting_balance) as monthly_usage,
                ROUND((GREATEST(0, l.current_balance - l.daily_starting_balance) / NULLIF(l.daily_limit, 0)) * 100, 2) as daily_utilization_percent,
                ROUND((GREATEST(0, l.current_balance - l.starting_balance) / NULLIF(l.monthly_limit, 0)) * 100, 2) as monthly_utilization_percent,
                GREATEST(0, l.daily_limit - l.current_balance) as daily_remaining,
                GREATEST(0, l.monthly_limit - l.current_balance) as monthly_remaining,
                b.name as branch_name,
                COUNT(t.id) as total_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN t.amount ELSE 0 END), 0) as total_transaction_amount
            FROM `lines` l
            LEFT JOIN branches b ON l.branch_id = b.id
            LEFT JOIN transactions t ON l.id = t.line_id
            GROUP BY l.id, l.mobile_number, l.network, l.status, l.current_balance, 
                     l.daily_limit, l.monthly_limit, l.daily_starting_balance, l.starting_balance, b.name
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_line_utilization');
        DB::statement('DROP VIEW IF EXISTS v_daily_transaction_summary');
        DB::statement('DROP VIEW IF EXISTS v_customer_summary');
        DB::statement('DROP VIEW IF EXISTS v_agent_performance');
        DB::statement('DROP VIEW IF EXISTS v_branch_performance');
        DB::statement('DROP VIEW IF EXISTS v_active_transactions');
    }
};
