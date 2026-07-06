<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$types = App\Models\MstRegulation::select('tipe')->distinct()->pluck('tipe');
echo "Distinct types of regulations:\n";
print_r($types->toArray());

$count = App\Models\MstRegulation::count();
echo "Total regulations: $count\n";

$first = App\Models\MstRegulation::first();
echo "First regulation details:\n";
print_r($first ? $first->toArray() : 'None');
