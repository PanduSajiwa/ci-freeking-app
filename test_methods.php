<?php
// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Direct test file
$testCases = [
    'App\Controllers\Vehicles' => 'delete',
    'App\Controllers\Customers' => 'delete',
    'App\Controllers\Users' => 'delete',
    'App\Controllers\ParkingUsage' => 'delete',
    'App\Controllers\ParkingSubmissions' => 'delete',
];

echo "Checking method signatures...\n";

foreach ($testCases as $className => $methodName) {
    try {
        $reflection = new ReflectionClass($className);
        $method = $reflection->getMethod($methodName);
        $params = $method->getParameters();
        $paramNames = implode(', ', array_map(function($p) { return '$' . $p->getName(); }, $params));
        echo "$className::$methodName - " . count($params) . " param(s): " . ($paramNames ?: 'none') . "\n";
    } catch (Exception $e) {
        echo "$className::$methodName - ERROR: " . $e->getMessage() . "\n";
    }
}
