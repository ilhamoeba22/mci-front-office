<?php
// Verify finished queue counts
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Queue;

echo "Queue Status Breakdown:\n";
echo "Status 0 (Waiting): " . Queue::where('st_antrian', 0)->count() . "\n";
echo "Status 2 (Called): " . Queue::where('st_antrian', 2)->count() . "\n";
echo "Status 3 (Finished): " . Queue::where('st_antrian', 3)->count() . "\n";
echo "Status 4 (Skipped): " . Queue::where('st_antrian', 4)->count() . "\n";
echo "\nType Breakdown (Finished Only):\n";
echo "CS Finished: " . Queue::where('type', 'CS')->where('st_antrian', 3)->count() . "\n";
echo "Teller Finished: " . Queue::where('type', 'Teller')->where('st_antrian', 3)->count() . "\n";
