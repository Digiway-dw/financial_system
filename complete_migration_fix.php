<?php

/**
 * Complete Migration Fix Script
 * This script performs a comprehensive cleanup of all migration issues
 */

$migrationsPath = __DIR__ . '/database/migrations/';

echo "Complete Migration Fix Script\n";
echo "=============================\n\n";

// Step 1: List all current migration files
echo "Step 1: Current migration files:\n";
$currentFiles = glob($migrationsPath . '*.php');
sort($currentFiles);

foreach ($currentFiles as $file) {
    echo "- " . basename($file) . "\n";
}

echo "\nStep 2: Removing problematic migration files...\n";

// List of ALL problematic migration files that need to be removed
$problematicFiles = [
    '2024_07_16_000001_add_key_column_to_session_settings_table.php',
    '2024_07_16_000003_add_is_enabled_to_working_hours_table.php',
    '2024_07_06_000000_create_branches_table.php',
    '2025_06_26_205815_add_role_to_users_table.php',
    '2025_06_26_210754_create_safes_table.php',
    '2025_06_26_210755_create_startup_safe_balances_table.php',
    '2025_07_05_141554_add_soft_deletes_to_users_table.php',
    '2025_07_05_141613_add_soft_deletes_to_users_table.php',
    '2025_07_09_162723_add_branch_id_to_transactions_table.php',
    '2025_07_09_150345_add_notes_to_transactions_table.php',
    '2025_07_15_151510_create_session_settings_table.php',
    '2025_07_20_211158_add_key_column_to_session_settings_table.php',
];

$removedCount = 0;

foreach ($problematicFiles as $file) {
    $fullPath = $migrationsPath . $file;
    if (file_exists($fullPath)) {
        if (unlink($fullPath)) {
            echo "✓ Removed: $file\n";
            $removedCount++;
        } else {
            echo "✗ Failed to remove: $file\n";
        }
    }
}

echo "\nStep 3: Scanning for any remaining problematic patterns...\n";

// Scan for any files with problematic patterns
$allFiles = glob($migrationsPath . '*.php');
$additionalProblematic = [];

foreach ($allFiles as $file) {
    $basename = basename($file);
    
    // Check for patterns that indicate problematic migrations
    if (strpos($basename, 'add_key_column_to_session_settings') !== false ||
        strpos($basename, 'add_is_enabled_to_working_hours') !== false) {
        $additionalProblematic[] = $basename;
    }
}

if (!empty($additionalProblematic)) {
    echo "Found additional problematic files:\n";
    foreach ($additionalProblematic as $file) {
        $fullPath = $migrationsPath . $file;
        if (unlink($fullPath)) {
            echo "✓ Removed: $file\n";
            $removedCount++;
        } else {
            echo "✗ Failed to remove: $file\n";
        }
    }
} else {
    echo "No additional problematic files found.\n";
}

echo "\nStep 4: Final migration file list:\n";
$finalFiles = glob($migrationsPath . '*.php');
sort($finalFiles);

foreach ($finalFiles as $file) {
    echo "- " . basename($file) . "\n";
}

echo "\n=============================\n";
echo "Summary:\n";
echo "- Total files removed: $removedCount\n";
echo "- Final migration count: " . count($finalFiles) . "\n";
echo "\nCleanup complete! You can now run 'php artisan migrate:fresh --seed'\n";
