<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Models\Queue;

echo "Checking database counts:\n";
echo "Setor: " . Transaction::count() . "\n";
echo "Tarik: " . Withdrawal::count() . "\n";
echo "Transfer: " . Transfer::count() . "\n";
echo "Queue: " . Queue::count() . "\n";
