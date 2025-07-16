<?php

// Test script to verify Receive transaction constraint fix
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // Try to create a test transaction with type 'Receive'
    $testData = [
        'customer_name' => 'Test Customer',
        'customer_mobile_number' => '1234567890',
        'customer_code' => 'TEST001',
        'amount' => 100,
        'commission' => 0,
        'deduction' => 0,
        'transaction_type' => 'Receive', // This should not fail anymore
        'agent_id' => 1,
        'status' => 'Completed',
        'transaction_date_time' => now(),
        'line_id' => 1,
        'safe_id' => 1,
        'is_absolute_withdrawal' => 0,
        'payment_method' => 'branch safe',
        'reference_number' => 'TEST' . time(),
        'branch_id' => 1,
    ];

    $transaction = \App\Models\Domain\Entities\Transaction::create($testData);
    echo "SUCCESS: Receive transaction created successfully with ID: " . $transaction->id . "\n";

    // Clean up test data
    $transaction->delete();
    echo "SUCCESS: Test transaction cleaned up.\n";
    echo "CONSTRAINT FIX VERIFIED: 'Receive' transaction type is now allowed!\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "CONSTRAINT FIX FAILED\n";
}
