<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Startup Safe Balances for today (" . now()->toDateString() . "):\n";

$records = \App\Models\StartupSafeBalance::where('date', now()->toDateString())->get();

foreach($records as $record) {
    $branchId = $record->branch_id ?? 'ALL';
    echo "Branch ID: {$branchId}, Balance: " . number_format($record->balance, 2) . "\n";
}

echo "\nCurrent Safe Balances:\n";
$safes = \App\Models\Domain\Entities\Safe::all();
foreach($safes as $safe) {
    echo "Safe: {$safe->name} (Branch: {$safe->branch_id}), Balance: " . number_format($safe->current_balance, 2) . "\n";
} 