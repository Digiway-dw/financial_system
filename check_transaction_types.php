<?php

use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->bind('path.config', function () {
    return __DIR__ . '/config';
});
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $types = DB::table('transactions')->distinct()->pluck('transaction_type');
    echo "Current transaction types in database: " . $types->implode(', ') . "\n";

    $cashTypes = DB::table('cash_transactions')->distinct()->pluck('transaction_type');
    echo "Current cash transaction types in database: " . $cashTypes->implode(', ') . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
