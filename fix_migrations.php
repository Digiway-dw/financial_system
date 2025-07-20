<?php

/**
 * Migration Cleanup Script
 * This script identifies and removes orphaned migration files that cause conflicts
 */

$migrationsPath = __DIR__ . '/database/migrations/';

// List of problematic migration files that should be removed
$problematicMigrations = [
    '2024_07_16_000001_add_key_column_to_session_settings_table.php',
    '2024_07_16_000003_add_is_enabled_to_working_hours_table.php',
    '2024_07_06_000000_create_branches_table.php', // Duplicate of 2024_07_04
    '2025_06_26_205815_add_role_to_users_table.php', // Empty duplicate
    '2025_06_26_210754_create_safes_table.php', // Duplicate of 2024_07_05
    '2025_06_26_210755_create_startup_safe_balances_table.php', // Duplicate of 2024_07_07
    '2025_07_05_141554_add_soft_deletes_to_users_table.php', // Empty duplicate
    '2025_07_05_141613_add_soft_deletes_to_users_table.php', // Empty duplicate
    '2025_07_09_162723_add_branch_id_to_transactions_table.php', // Empty duplicate
    '2025_07_09_150345_add_notes_to_transactions_table.php', // Duplicate
    '2025_07_15_151510_create_session_settings_table.php', // Older version
    '2025_07_20_211158_add_key_column_to_session_settings_table.php', // Redundant
];

echo "Migration Cleanup Script\n";
echo "========================\n\n";

$removedCount = 0;
$notFoundCount = 0;

foreach ($problematicMigrations as $migration) {
    $filePath = $migrationsPath . $migration;
    
    if (file_exists($filePath)) {
        if (unlink($filePath)) {
            echo "✓ Removed: $migration\n";
            $removedCount++;
        } else {
            echo "✗ Failed to remove: $migration\n";
        }
    } else {
        echo "- Not found: $migration\n";
        $notFoundCount++;
    }
}

echo "\n========================\n";
echo "Summary:\n";
echo "- Files removed: $removedCount\n";
echo "- Files not found: $notFoundCount\n";
echo "- Total processed: " . count($problematicMigrations) . "\n";

// List remaining migration files
echo "\nRemaining migration files:\n";
$remainingFiles = glob($migrationsPath . '*.php');
sort($remainingFiles);

foreach ($remainingFiles as $file) {
    echo "- " . basename($file) . "\n";
}

echo "\nCleanup complete!\n";
