<?php
// Load Laravel
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use App\Models\Queue;

echo "--- DEBUGGING QUEUE DATA ---\n";

// 1. Check Types
$types = Queue::distinct()->pluck('type');
echo "Available Types: " . $types->implode(', ') . "\n";

// 2. Check Sample Data for Teller
echo "\n--- SAMPLE TELLER DATA ---\n";
$samples = Queue::where('type', 'Teller')
                ->whereNotNull('kode')
                ->orderBy('id_antrian', 'desc')
                ->take(5)
                ->get();

if ($samples->isEmpty()) {
    echo "NO DATA FOUND for type='Teller' with kode not null.\n";
} else {
    foreach ($samples as $q) {
        echo "ID: {$q->id_antrian} | Kode: [{$q->kode}] | Type: {$q->type}\n";
    }
}

// 3. Test Filter Logic
echo "\n--- TESTING FILTERS ---\n";
$st = Queue::where('type', 'Teller')->where('kode', 'like', 'ST-%')->count();
$tt = Queue::where('type', 'Teller')->where('kode', 'like', 'TT-%')->count();
$on = Queue::where('type', 'Teller')->where('kode', 'like', 'ON-%')->count();

echo "Setor Tunai (ST-%): $st\n";
echo "Tarik Tunai (TT-%): $tt\n";
echo "Transfer (ON-%):    $on\n";
