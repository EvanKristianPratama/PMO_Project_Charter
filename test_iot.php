<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$service = app(App\Services\StrategicHouse\StrategicHousePageService::class);
$props = $service->getPageProps(['view' => 'mapping', 'initiative_type' => 1], ['technologyCards']);

$technologyCards = $props['technologyCards'];
if ($technologyCards instanceof Closure) {
    $technologyCards = $technologyCards();
}

$iotCard = collect($technologyCards)->firstWhere('name', 'IoTs');

if ($iotCard) {
    echo "SUCCESS: IoTs card found.\n";
    echo "Display Name: " . $iotCard['display_name'] . "\n";
    echo "Initiatives count: " . $iotCard['initiatives_count'] . "\n";
} else {
    echo "FAILURE: IoTs card not found.\n";
    print_r(array_map(fn($c) => is_array($c) ? $c['name'] : 'Not an array', $technologyCards));
}
