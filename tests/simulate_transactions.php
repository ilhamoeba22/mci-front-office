<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Models\Queue;
use Carbon\Carbon;

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- STARTING TRANSACTION SIMULATION ---\n";
$accountNo = '1180100251';
$dummyName = 'DUMMY USER';

$controller = app(TransactionController::class);

// 1. SETOR TUNAI
echo "\n[1] Testing Setor Tunai (Deposit)...\n";
$reqDeposit = Request::create('/transaction/deposit/store', 'POST', [
    'nama' => $dummyName,
    'no_rek' => $accountNo,
    'nominal' => 50000,
    'nama_penyetor' => 'Tester',
    'hp_penyetor' => '08123456789',
    'tujuan' => 'Test Setor'
]);

try {
    $controller->storeDeposit($reqDeposit);

    // Verify DB (latest by id_setor)
    $trx = Transaction::where('no_rek', $accountNo)->orderBy('id_setor', 'desc')->first();
    if ($trx && $trx->nominal == 50000) {
        echo "✅ Setor Tunai SUCCESS. Token: {$trx->token}, Nominal: {$trx->nominal}\n";
    }
    else {
        echo "❌ Setor Tunai FAILED (DB Record not found).\n";
        if (session('errors'))
            dump(session('errors')->all());
    }
}
catch (\Exception $e) {
    echo "❌ Setor Tunai ERROR: " . $e->getMessage() . "\n";
}

// 2. TARIK TUNAI
echo "\n[2] Testing Tarik Tunai (Withdrawal)...\n";
$reqWithdraw = Request::create('/transaction/withdrawal/store', 'POST', [
    'nama' => $dummyName,
    'no_rek' => $accountNo,
    'nominal' => 100000,
    'nama_penarik' => 'Tester',
    'hp_penarik' => '08123456789',
    'noid_penarik' => '1234567890',
    'alamat_penarik' => 'Jl. Test No. 1'
]);

try {
    $controller->storeWithdrawal($reqWithdraw);

    // Verify DB (latest by id_tarik)
    $wd = Withdrawal::where('no_rek', $accountNo)->orderBy('id_tarik', 'desc')->first();
    if ($wd && $wd->nominal == 100000) {
        echo "✅ Tarik Tunai SUCCESS. Token: {$wd->token}, ID: {$wd->id_tarik}\n";
    }
    else {
        echo "❌ Tarik Tunai FAILED. (Record not found in tbl_tarik)\n";
        if (session('errors'))
            dump(session('errors')->all());
    }
}
catch (\Exception $e) {
    echo "❌ Tarik Tunai ERROR: " . $e->getMessage() . "\n";
}

// 3. TRANSFER
echo "\n[3] Testing Transfer...\n";
$reqTransfer = Request::create('/transaction/transfer/store', 'POST', [
    'nama' => $dummyName,
    'no_rek' => $accountNo,
    'nominal' => 25000,
    'bank_tujuan' => 'BCA/6500',
    'no_rek_tujuan' => '0987654321',
    'nama_tujuan' => 'Penerima Test',
    'nama_penyetor' => 'Tester',
    'hp_penyetor' => '08123456789',
    'alamat_penyetor' => 'Jl. Test'
]);

try {
    $controller->storeTransfer($reqTransfer);

    // Verify DB (latest by id_trf)
    $trf = Transfer::where('no_rek', $accountNo)->orderBy('id_trf', 'desc')->first();
    if ($trf && $trf->nominal == 25000) {
        echo "✅ Transfer SUCCESS. Token: {$trf->token}, Bank: {$trf->bank_tujuan}\n";
    }
    else {
        echo "❌ Transfer FAILED. (Record not found in tbl_transfer)\n";
        if (session('errors'))
            dump(session('errors')->all());
    }
}
catch (\Exception $e) {
    echo "❌ Transfer ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- SIMULATION COMPLETE ---\n";
