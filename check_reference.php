<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Domain\Entities\Transaction;

// Check if the problematic reference number exists
$existingTransaction = Transaction::where('reference_number', 'RF123-20250709-000001')->first();

if ($existingTransaction) {
    echo "Found existing transaction with reference number RF123-20250709-000001:\n";
    echo "ID: " . $existingTransaction->id . "\n";
    echo "Customer: " . $existingTransaction->customer_name . "\n";
    echo "Amount: " . $existingTransaction->amount . "\n";
    echo "Created: " . $existingTransaction->created_at . "\n";
} else {
    echo "No transaction found with reference number RF123-20250709-000001\n";
}

// Check all transactions with reference numbers
$allTransactions = Transaction::whereNotNull('reference_number')->get(['id', 'reference_number', 'customer_name', 'created_at']);
echo "\nAll transactions with reference numbers:\n";
foreach ($allTransactions as $trans) {
    echo "ID: {$trans->id}, Ref: {$trans->reference_number}, Customer: {$trans->customer_name}, Created: {$trans->created_at}\n";
}
