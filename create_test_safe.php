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

echo "=== Creating Test Safe ===\n\n";

try {
    $branch = Branch::first();
    if (!$branch) {
        echo "❌ No branch found\n";
        exit;
    }

    // Create a test safe
    $safe = Safe::create([
        'name' => 'Main Safe',
        'current_balance' => 50000.00,
        'branch_id' => $branch->id,
        'type' => 'main',
    ]);

    echo "✅ Created test safe:\n";
    echo "   Safe ID: {$safe->id}\n";
    echo "   Name: {$safe->name}\n";
    echo "   Balance: " . number_format($safe->current_balance, 2) . " EGP\n";
    echo "   Branch: {$branch->name}\n\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
