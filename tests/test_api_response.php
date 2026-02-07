<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Http\Request;
use App\Http\Controllers\AdminController;

echo "--- API RESPONSE SIMULATION ---\n";

$controller = new AdminController();

// Test 'daily' filter
echo "\n[TEST 1] Filter: daily\n";
$request = Request::create('/chart-data', 'GET', ['filter' => 'daily']);
$response = $controller->getChartData($request);
$content = $response->getContent();
$data = json_decode($content, true);

echo "Summary Total Tx: " . $data['summary']['total_tx'] . "\n";
echo "Summary Total Nominal: " . $data['summary']['total_nominal'] . "\n";
echo "Candles Count: " . count($data['candles']) . "\n";

if (count($data['candles']) > 0) {
    echo "First Candle: " . json_encode($data['candles'][0]) . "\n";
}
else {
    echo "NO CANDLES RETURNED!\n";
}

// Test 'weekly' filter
echo "\n[TEST 2] Filter: weekly\n";
$request = Request::create('/chart-data', 'GET', ['filter' => 'weekly']);
$response = $controller->getChartData($request);
$content = $response->getContent();
$data = json_decode($content, true);

echo "Summary Total Tx: " . $data['summary']['total_tx'] . "\n";
echo "Candles Count: " . count($data['candles']) . "\n";
