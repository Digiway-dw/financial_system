<?php
// Run with: php scripts/fix_line_transfer_notifications.php
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Update all notifications with 'line transfer' in the message to have type 'line_transfer'
DB::table('notifications')
    ->where('type', 'App\\Notifications\\AdminNotification')
    ->whereRaw("JSON_EXTRACT(data, '$.message') LIKE '%line transfer%'")
    ->whereRaw("JSON_EXTRACT(data, '$.type') IS NULL")
    ->update([
        'data' => DB::raw("JSON_SET(data, '$.type', 'line_transfer')")
    ]);

// Also handle Arabic message
DB::table('notifications')
    ->where('type', 'App\\Notifications\\AdminNotification')
    ->whereRaw("JSON_EXTRACT(data, '$.message') LIKE '%خط إلى خط%'")
    ->whereRaw("JSON_EXTRACT(data, '$.type') IS NULL")
    ->update([
        'data' => DB::raw("JSON_SET(data, '$.type', 'line_transfer')")
    ]);

echo "Line transfer notifications updated.\n";
