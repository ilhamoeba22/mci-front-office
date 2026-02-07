<?php

use App\Services\CoreBankingService;
use Illuminate\Support\Facades\Log;

require __DIR__ . '/../vendor/autoload.php';

$app = require __DIR__ . '/../bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- STARTING API CHECK ---\n";
echo "Account: 1180100251\n";

try {
    $service = new CoreBankingService();
    echo "Service instantiated.\n";

    echo "Calling getBalance()...\n";
    $result = $service->getBalance('1180100251');

    echo "--- API RESPONSE ---\n";
    print_r($result);
    echo "--------------------\n";

    if (isset($result['status']) && $result['status'] === 'error') {
        echo "❌ API Check FAILED: " . $result['message'] . "\n";
    }
    elseif (isset($result['responseCode']) && $result['responseCode'] != '00') {
        echo "❌ API Check FAILED (Response Code " . $result['responseCode'] . "): " . ($result['responseMessage'] ?? 'Unknown Error') . "\n";
    }
    else {
        echo "✅ API Check SUCCESS.\n";
    }

}
catch (\Exception $e) {
    echo "❌ EXCEPTION: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
