<?php

// This script updates the v_line_utilization view to use the correct calculation for daily and monthly remaining
// based on starting balances rather than usage fields

// Bootstrap the Laravel application
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Updating v_line_utilization view...\n";

try {
    // Drop the existing view
    DB::statement('DROP VIEW IF EXISTS v_line_utilization');
    echo "Dropped existing view.\n";
    
    // Create the updated view
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
    
    echo "View updated successfully!\n";
    
    // Test the view by querying it
    $result = DB::select("SELECT * FROM v_line_utilization LIMIT 3");
    
    echo "\nSample data from updated view:\n";
    foreach ($result as $row) {
        echo "Line: {$row->mobile_number}\n";
        echo "  Current Balance: {$row->current_balance}\n";
        echo "  Daily Starting Balance: {$row->daily_starting_balance}\n";
        echo "  Daily Usage: {$row->daily_usage}\n";
        echo "  Daily Limit: {$row->daily_limit}\n";
        echo "  Daily Remaining: {$row->daily_remaining}\n";
        echo "  Monthly Starting Balance: {$row->starting_balance}\n";
        echo "  Monthly Usage: {$row->monthly_usage}\n";
        echo "  Monthly Limit: {$row->monthly_limit}\n";
        echo "  Monthly Remaining: {$row->monthly_remaining}\n";
        echo "\n";
    }
    
} catch (\Exception $e) {
    echo "Error updating view: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

echo "\nScript completed at " . now()->toDateTimeString() . "\n"; 