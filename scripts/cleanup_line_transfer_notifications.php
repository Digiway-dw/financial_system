<?php
// Run with: php scripts/cleanup_line_transfer_notifications.php
use Illuminate\Support\Facades\DB;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Update all notifications related to line transfers to have type 'line_transfer'
$updated = DB::table('notifications')
    ->where('type', 'App\\Notifications\\AdminNotification')
    ->where(function($query) {
        $query->whereRaw("JSON_EXTRACT(data, '$.message') LIKE '%line%'")
              ->orWhereRaw("JSON_EXTRACT(data, '$.message') LIKE '%خط%'")
              ->orWhereRaw("JSON_EXTRACT(data, '$.message') LIKE '%Line Transfer%'");
    })
    ->update([
        'data' => DB::raw("JSON_SET(data, '$.type', 'line_transfer')")
    ]);

echo "Updated $updated line transfer notifications.\n";

// Alternatively, just delete all line transfer notifications
$deleted = DB::table('notifications')
    ->where('type', 'App\\Notifications\\AdminNotification')
    ->where(function($query) {
        $query->whereRaw("JSON_EXTRACT(data, '$.message') LIKE '%line%'")
              ->orWhereRaw("JSON_EXTRACT(data, '$.message') LIKE '%خط%'")
              ->orWhereRaw("JSON_EXTRACT(data, '$.message') LIKE '%Line Transfer%'");
    })
    ->delete();

echo "Deleted $deleted line transfer notifications.\n";
