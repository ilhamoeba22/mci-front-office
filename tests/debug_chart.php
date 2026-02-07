<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Carbon\Carbon;

echo "--- DEBUG CHART DATA ---\n";

$now = Carbon::now();
echo "Current Time: " . $now->format('Y-m-d H:i:s') . "\n";

// 1. Check Counts
$countSetor = \App\Models\Transaction::count();
$countTarik = \App\Models\Withdrawal::count();
$countTransfer = \App\Models\Transfer::count();

echo "Total Setor: $countSetor\n";
echo "Total Tarik: $countTarik\n";
echo "Total Transfer: $countTransfer\n";

// 2. Check Sample Data (Dates)
$tx = \App\Models\Transaction::latest('created')->first();
if ($tx) {
    echo "\nLatest Setor:\n";
    echo "ID: " . $tx->id_setor . "\n";
    echo "Created (Model Attr): " . $tx->created . "\n";
    echo "Tgl: " . $tx->tgl . "\n";
    echo "Raw: " . json_encode($tx) . "\n";
}
else {
    echo "\nLatest Setor: NULL\n";
}

// 3. Test Logic from Controller
$startDate = $now->copy()->subDays(30)->startOfDay();
$endDate = $now->copy()->endOfDay();
echo "\nFilter Range (Daily): " . $startDate->format('Y-m-d H:i:s') . " - " . $endDate->format('Y-m-d H:i:s') . "\n";

$all = collect();
$txs = \App\Models\Transaction::select('created as created_at', 'nominal')->get();
$all = $all->concat($txs);

$filtered = $all->filter(function ($item) use ($startDate, $endDate) {
    if (!$item->created_at)
        return false;
    $date = is_string($item->created_at) ?Carbon::parse($item->created_at) : $item->created_at;
    return $date >= $startDate && $date <= $endDate;
});

echo "Total Loaded: " . $all->count() . "\n";
echo "Total Filtered (Last 30 Days): " . $filtered->count() . "\n";

if ($filtered->count() > 0) {
    echo "Sample Filtered Item Date: " . $filtered->first()->created_at . "\n";
}
