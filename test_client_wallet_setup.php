<?php

use Illuminate\Support\Facades\DB;
use App\Models\Domain\Entities\Customer;
use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Safe;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->bind('path.config', function () {
    return __DIR__ . '/config';
});
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== Client Wallet Withdrawal Test Setup ===\n\n";

try {
    // Create a test customer with balance
    $customer = Customer::firstOrCreate(
        ['mobile_number' => '01234567890'],
        [
            'name' => 'Test Client',
            'customer_code' => 'TEST001',
            'balance' => 1000.00,
            'branch_id' => Branch::first()->id,
        ]
    );

    // Update balance if customer already existed
    if ($customer->balance < 500) {
        $customer->balance = 1000.00;
        $customer->save();
    }

    echo "✅ Created test customer:\n";
    echo "   Name: {$customer->name}\n";
    echo "   Code: {$customer->customer_code}\n";
    echo "   Mobile: {$customer->mobile_number}\n";
    echo "   Balance: " . number_format($customer->balance, 2) . " EGP\n\n";

    // Check safes
    $safes = Safe::all();
    echo "✅ Available safes:\n";
    foreach ($safes as $safe) {
        echo "   Safe ID: {$safe->id}, Name: {$safe->name}, Balance: " . number_format($safe->current_balance, 2) . " EGP\n";
    }
    echo "\n";

    echo "🎯 **Client Wallet Withdrawal is ready for testing!**\n\n";
    echo "Test Steps:\n";
    echo "1. Go to /transactions/cash/withdrawal\n";
    echo "2. Select 'Client Wallet' withdrawal type\n";
    echo "3. Search for customer: {$customer->mobile_number} or {$customer->customer_code}\n";
    echo "4. Enter amount less than {$customer->balance} EGP\n";
    echo "5. Fill required fields and submit\n";
    echo "6. Verify customer balance decreases immediately\n";
    echo "7. Verify safe balance remains unchanged\n";
    echo "8. If pending, approve the transaction and verify no additional changes\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
