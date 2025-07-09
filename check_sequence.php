<?php
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Domain\Entities\Branch;
use App\Models\Domain\Entities\Transaction;

// Check what branch has code RF123
$branch = Branch::where('branch_code', 'RF123')->first();
if ($branch) {
    echo "Branch RF123: {$branch->name} (ID: {$branch->id})\n";
} else {
    echo "No branch found with code RF123\n";
}

// Let's see what the next sequence should be for today
$today = date('Ymd');
$pattern = "RF123-{$today}-%";
$lastRef = Transaction::where('reference_number', 'like', $pattern)
    ->orderBy('reference_number', 'desc')
    ->first();

if ($lastRef) {
    echo "Last reference number for today: {$lastRef->reference_number}\n";
    $lastSeq = intval(substr($lastRef->reference_number, -6));
    $nextSeq = $lastSeq + 1;
    echo "Next sequence should be: " . str_pad($nextSeq, 6, '0', STR_PAD_LEFT) . "\n";
    echo "Next reference number should be: RF123-{$today}-" . str_pad($nextSeq, 6, '0', STR_PAD_LEFT) . "\n";
} else {
    echo "No transactions found for today with RF123 prefix\n";
}
