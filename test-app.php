<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
echo "App class: " . get_class($app) . "\n";
echo "Is 'files' aliased? " . ($app->isAlias('files') ? 'Yes' : 'No') . "\n";
if ($app->isAlias('files')) {
    echo "Alias for 'files': " . $app->getAlias('files') . "\n";
}
try {
    echo "Files class: " . get_class($app->make('files')) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
