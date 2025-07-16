<?php

use Illuminate\Support\Facades\DB;

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->bind('path.config', function () {
    return __DIR__ . '/config';
});
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    // Check the table structure
    $createTable = DB::select("SHOW CREATE TABLE branches");
    echo "Table structure:\n";
    echo $createTable[0]->{'Create Table'} . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
