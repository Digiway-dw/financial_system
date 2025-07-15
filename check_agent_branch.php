<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Find agent users
$agents = \App\Domain\Entities\User::whereHas('roles', function($q) {
    $q->where('name', 'agent');
})->get();

echo "Agent Users:\n";
foreach($agents as $agent) {
    echo "Agent: {$agent->name}, Branch ID: {$agent->branch_id}, Branch Name: " . ($agent->branch->name ?? 'N/A') . "\n";
}

echo "\nAll Branches:\n";
$branches = \App\Models\Domain\Entities\Branch::all();
foreach($branches as $branch) {
    echo "Branch ID: {$branch->id}, Name: {$branch->name}\n";
}

echo "\nStartup Safe Balances for today:\n";
$records = \App\Models\StartupSafeBalance::where('date', now()->toDateString())->get();
foreach($records as $record) {
    $branchId = $record->branch_id ?? 'ALL';
    $branchName = $record->branch_id ? \App\Models\Domain\Entities\Branch::find($record->branch_id)->name ?? 'Unknown' : 'ALL BRANCHES';
    echo "Branch: {$branchName} (ID: {$branch_id}), Balance: " . number_format($record->balance, 2) . "\n";
} 