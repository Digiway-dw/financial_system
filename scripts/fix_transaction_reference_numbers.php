<?php

use App\Models\Domain\Entities\Transaction;
use App\Helpers\helpers;

require __DIR__ . '/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$updated = 0;

Transaction::whereNull('reference_number')->orWhere('reference_number', '')->chunk(100, function ($transactions) use (&$updated) {
    foreach ($transactions as $transaction) {
        $branchName = $transaction->branch->name ?? 'Unknown';
        $transaction->reference_number = generate_reference_number($branchName);
        $transaction->save();
        $updated++;
    }
});

echo "Updated $updated transactions with reference numbers.\n"; 