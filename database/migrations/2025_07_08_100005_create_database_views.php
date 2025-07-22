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
        DB::statement("
            CREATE VIEW v_active_transactions AS
            SELECT 
                t.id,
                t.customer_name,
                t.customer_mobile_number,
                t.amount,
                t.commission,
                t.deduction,
                t.transaction_type,
                t.status,
                t.transaction_date_time,
                COALESCE(t.payment_method, 'cash') as payment_method,
                u.name as agent_name,
                u.email as agent_email,
                b.name as branch_name,
                b.branch_code,
                s.name as safe_name,
                COALESCE(l.mobile_number, '') as line_mobile,
                COALESCE(l.network, '') as line_network
            FROM transactions t
            LEFT JOIN users u ON t.agent_id = u.id
            LEFT JOIN branches b ON u.branch_id = b.id
            LEFT JOIN safes s ON t.safe_id = s.id
            LEFT JOIN `lines` l ON t.line_id = l.id
        ");

        // Create view for branch performance summary
        DB::statement("DROP VIEW IF EXISTS v_branch_performance");
        DB::statement("
            CREATE VIEW v_branch_performance AS
            SELECT 
                b.id as branch_id,
                b.name as branch_name,
                b.branch_code,
                COUNT(t.id) as total_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END), 0) as pending_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN t.amount ELSE 0 END), 0) as total_amount,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN t.commission ELSE 0 END), 0) as total_commission,
                COALESCE(AVG(CASE WHEN t.status = 'completed' THEN t.amount ELSE NULL END), 0) as avg_transaction_amount,
                COUNT(DISTINCT u.id) as active_agents,
                COUNT(DISTINCT DATE(t.transaction_date_time)) as active_days,
                COALESCE(s.current_balance, 0) as safe_balance
            FROM branches b
            LEFT JOIN users u ON b.id = u.branch_id
            LEFT JOIN transactions t ON u.id = t.agent_id
            LEFT JOIN safes s ON b.id = s.branch_id
            GROUP BY b.id, b.name, b.branch_code, s.current_balance
        ");

        // Create view for agent performance summary
        DB::statement("DROP VIEW IF EXISTS v_agent_performance");
        DB::statement("
            CREATE VIEW v_agent_performance AS
            SELECT 
                u.id as agent_id,
                u.name as agent_name,
                u.email as agent_email,
                b.name as branch_name,
                b.id as branch_id,
                COUNT(t.id) as total_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'pending' THEN 1 ELSE 0 END), 0) as pending_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'rejected' THEN 1 ELSE 0 END), 0) as rejected_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN t.amount ELSE 0 END), 0) as total_amount,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN t.commission ELSE 0 END), 0) as total_commission,
                COALESCE(AVG(CASE WHEN t.status = 'completed' THEN t.amount ELSE NULL END), 0) as avg_transaction_amount,
                COUNT(DISTINCT DATE(t.transaction_date_time)) as active_days,
                MAX(t.transaction_date_time) as last_transaction_date
            FROM users u
            LEFT JOIN branches b ON u.branch_id = b.id
            LEFT JOIN transactions t ON u.id = t.agent_id
            GROUP BY u.id, u.name, u.email, b.name, b.id
        ");

        // Create view for customer transaction summary
        DB::statement("DROP VIEW IF EXISTS v_customer_summary");
        DB::statement("
            CREATE VIEW v_customer_summary AS
            SELECT 
                c.id as customer_id,
                c.name as customer_name,
                c.mobile_number,
                COALESCE(c.customer_code, '') as customer_code,
                COALESCE(c.balance, 0) as balance,
                COALESCE(c.is_client, 0) as is_client,
                COUNT(t.id) as total_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN 1 ELSE 0 END), 0) as completed_transactions,
                COALESCE(SUM(CASE WHEN t.status = 'completed' THEN t.amount ELSE 0 END), 0) as total_transaction_amount,
                COALESCE(AVG(CASE WHEN t.status = 'completed' THEN t.amount ELSE NULL END), 0) as avg_transaction_amount,
                MAX(t.transaction_date_time) as last_transaction_date,
                MIN(t.transaction_date_time) as first_transaction_date,
                COUNT(DISTINCT t.agent_id) as agents_worked_with,
                COUNT(DISTINCT DATE(t.transaction_date_time)) as transaction_days
            FROM customers c
            LEFT JOIN transactions t ON (c.mobile_number = t.customer_mobile_number OR c.customer_code = t.customer_code)
            GROUP BY c.id, c.name, c.mobile_number, c.customer_code, c.balance, c.is_client
        ");

        // Create view for daily transaction summary
        DB::statement("DROP VIEW IF EXISTS v_daily_transaction_summary");
        DB::statement("
            CREATE VIEW v_daily_transaction_summary AS
            SELECT 
                DATE(t.transaction_date_time) as transaction_date,
                t.transaction_type,
                t.status,
                b.id as branch_id,
                b.name as branch_name,
                COUNT(t.id) as transaction_count,
                COALESCE(SUM(t.amount), 0) as total_amount,
                COALESCE(SUM(t.commission), 0) as total_commission,
                COALESCE(SUM(t.deduction), 0) as total_deductions,
                COALESCE(AVG(t.amount), 0) as avg_amount,
                COALESCE(MIN(t.amount), 0) as min_amount,
                COALESCE(MAX(t.amount), 0) as max_amount,
                COUNT(DISTINCT t.agent_id) as agents_involved,
                COUNT(DISTINCT t.customer_mobile_number) as unique_customers
            FROM transactions t
            LEFT JOIN users u ON t.agent_id = u.id
            LEFT JOIN branches b ON u.branch_id = b.id
            GROUP BY DATE(t.transaction_date_time), t.transaction_type, t.status, b.id, b.name
        ");

        // Create view for line utilization
        DB::statement("DROP VIEW IF EXISTS v_line_utilization");
        DB::statement("
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
        ");
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
