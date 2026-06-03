<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// SQLite - find tables with 'steering'
$tables = \DB::select("SELECT name FROM sqlite_master WHERE type='table' AND name LIKE '%steering%'");
foreach ($tables as $t) {
    echo "Found table: " . $t->name . PHP_EOL;
    
    // Get columns
    $columns = \Schema::getColumnListing($t->name);
    echo "Columns: " . json_encode($columns) . PHP_EOL;
    
    // Get data
    $data = \DB::table($t->name)->get();
    echo "Row count: " . $data->count() . PHP_EOL;
    echo "Data: " . json_encode($data, JSON_PRETTY_PRINT) . PHP_EOL;
}

if (empty($tables)) {
    echo "No tables found with 'steering' in name" . PHP_EOL;
    // List all tables
    $allTables = \DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");
    echo "All tables:" . PHP_EOL;
    foreach ($allTables as $t) {
        if (stripos($t->name, 'mst') !== false || stripos($t->name, 'it') !== false) {
            echo "  - " . $t->name . PHP_EOL;
        }
    }
}
