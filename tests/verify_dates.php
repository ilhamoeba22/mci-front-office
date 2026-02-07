<?php
// Quick date range verification
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$dates = DB::select("
    SELECT 
        MIN(created) as earliest,
        MAX(created) as latest,
        COUNT(*) as total
    FROM (
        SELECT created FROM tbl_setor
        UNION ALL
        SELECT created FROM tbl_tarik
        UNION ALL
        SELECT created FROM tbl_transfer
    ) as all_tx
");

echo "Date Range Verification:\n";
echo "Earliest: " . $dates[0]->earliest . "\n";
echo "Latest: " . $dates[0]->latest . "\n";
echo "Total Transactions: " . $dates[0]->total . "\n";
