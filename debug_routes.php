<?php
// Simple debug script to test routes and controllers
// Run: http://localhost:8080/debug_routes.php

echo "<h2>Testing Delete Routes</h2>";

// Test if methods exist
$testCases = [
    'App\Controllers\Vehicles' => 'delete',
    'App\Controllers\Customers' => 'delete',
    'App\Controllers\Users' => 'delete',
    'App\Controllers\ParkingUsage' => 'delete',
    'App\Controllers\ParkingSubmissions' => 'delete',
];

echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Controller</th><th>Method</th><th>Status</th></tr>";

foreach ($testCases as $className => $methodName) {
    try {
        $reflection = new ReflectionClass($className);
        $method = $reflection->getMethod($methodName);
        $params = $method->getParameters();
        $paramCount = count($params);
        
        $status = "✓ OK - " . $paramCount . " param(s)";
        if ($paramCount === 0) {
            $status = "<span style='color:red'>✗ ERROR - No parameters (expected 1)</span>";
        }
        
        echo "<tr>";
        echo "<td>$className</td>";
        echo "<td>$methodName()</td>";
        echo "<td>$status</td>";
        echo "</tr>";
    } catch (Exception $e) {
        echo "<tr>";
        echo "<td>$className</td>";
        echo "<td>$methodName()</td>";
        echo "<td><span style='color:red'>✗ ERROR - " . $e->getMessage() . "</span></td>";
        echo "</tr>";
    }
}

echo "</table>";

echo "<h2>Route Configuration Test</h2>";
echo "<p>If you see this page, the web server is running. Check app/Config/Routes.php for route definitions.</p>";
echo "<p>Expected routes:</p>";
echo "<ul>";
echo "<li>POST /vehicles/delete/{id}</li>";
echo "<li>POST /customers/delete/{id}</li>";
echo "<li>POST /users/delete/{id}</li>";
echo "<li>POST /parkingusage/delete/{id}</li>";
echo "<li>POST /parkingsubmissions/delete/{id}</li>";
echo "</ul>";

?>
