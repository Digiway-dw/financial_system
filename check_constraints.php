<?php

use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->bind('path.config', function () {
    return __DIR__ . '/config';
});
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Check if tables exist
    $tableExists = DB::select("SHOW TABLES LIKE 'transactions'");
    if (empty($tableExists)) {
        echo "Transactions table does not exist\n";
        exit;
    }

    // Check transaction types and count
    $types = DB::table('transactions')
        ->select('transaction_type', DB::raw('COUNT(*) as count'))
        ->groupBy('transaction_type')
        ->get();

    echo "Transaction types and counts:\n";
    foreach ($types as $type) {
        echo "  {$type->transaction_type}: {$type->count}\n";
    }

    // Check if constraint exists
    $constraints = DB::select("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.TABLE_CONSTRAINTS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'transactions' 
        AND CONSTRAINT_NAME LIKE '%transaction_type%'
    ");

    echo "\nExisting constraints:\n";
    foreach ($constraints as $constraint) {
        echo "  {$constraint->CONSTRAINT_NAME}\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
