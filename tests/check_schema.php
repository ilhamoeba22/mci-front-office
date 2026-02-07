<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "--- SCHEMA AND DATA CHECK ---\n";

// 1. Check Table Columns
$columns = Schema::getColumnListing('tbl_setor');
echo "Columns in tbl_setor: " . implode(', ', $columns) . "\n";

if (!in_array('created', $columns)) {
    echo "CRITICAL: 'created' column MISSING in tbl_setor!\n";
}
else {
    echo "OK: 'created' column exists.\n";
}

// 2. Check Raw Data
$sample = DB::table('tbl_setor')->select('id_setor', 'tgl', 'created')->orderBy('id_setor', 'desc')->first();
if ($sample) {
    echo "Sample Row (Latest):\n";
    echo "ID: " . $sample->id_setor . "\n";
    echo "Tgl: " . $sample->tgl . "\n";
    echo "Created: " . $sample->created . "\n";
}
else {
    echo "Table is empty.\n";
}

// 3. Test Controller Logic Simulation
echo "\n--- CONTROLLER LOGIC SIMULATION ---\n";
$txs = \App\Models\Transaction::select('created as created_at', 'nominal')->orderBy('created', 'desc')->take(5)->get();
echo "Model Results (Top 5):\n";
foreach ($txs as $tx) {
    echo "Date: " . $tx->created_at . " | Nominal: " . $tx->nominal . "\n";
}

// 4. Test Filtering
$now = \Carbon\Carbon::now();
$start = $now->copy()->subDays(30);
$count = \App\Models\Transaction::where('created', '>=', $start)->count();
echo "\nRecords in last 30 days (via Eloquent): $count\n";
