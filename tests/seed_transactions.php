<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Carbon\Carbon;

echo "--- STARTING DUMMY SEEDING (5x EACH) ---\n";
$accountNo = '1180100251';
$dummyName = 'DUMMY USER';
$controller = app(TransactionController::class);

$banks = ['BCA', 'BNI', 'MANDIRI', 'BRI', 'CIMB'];

// 1. SETOR TUNAI (5x)
echo "\n[1] Seeding Setor Tunai...\n";
for ($i = 1; $i <= 5; $i++) {
    try {
        $req = Request::create('/transaction/deposit/store', 'POST', [
            'nama' => $dummyName,
            'no_rek' => $accountNo,
            'nominal' => 50000 * $i,
            'nama_penyetor' => 'Seeder ' . $i,
            'hp_penyetor' => '08123456789',
            'tujuan' => 'Setor Dummy ' . $i
        ]);
        $controller->storeDeposit($req);
        echo "✅ Setor $i Saved.\n";
    }
    catch (\Exception $e) {
        echo "❌ Setor $i Failed: " . $e->getMessage() . "\n";
    }
}

// 2. TARIK TUNAI (5x)
echo "\n[2] Seeding Tarik Tunai...\n";
for ($i = 1; $i <= 5; $i++) {
    try {
        $req = Request::create('/transaction/withdrawal/store', 'POST', [
            'nama' => $dummyName,
            'no_rek' => $accountNo,
            'nominal' => 100000 * $i,
            'nama_penarik' => 'Seeder ' . $i,
            'hp_penarik' => '08123456789',
            'noid_penarik' => '1234567890',
            'alamat_penarik' => 'Jl. Dummy No. ' . $i
        ]);
        $controller->storeWithdrawal($req);
        echo "✅ Tarik $i Saved.\n";
    }
    catch (\Exception $e) {
        echo "❌ Tarik $i Failed: " . $e->getMessage() . "\n";
    }
}

// 3. TRANSFER (5x)
echo "\n[3] Seeding Transfer...\n";
for ($i = 1; $i <= 5; $i++) {
    try {
        $bank = $banks[($i - 1) % count($banks)];
        $req = Request::create('/transaction/transfer/store', 'POST', [
            'nama' => $dummyName,
            'no_rek' => $accountNo,
            'nominal' => 25000 * $i,
            'bank_tujuan' => "$bank/6500",
            'no_rek_tujuan' => '098765432' . $i,
            'nama_tujuan' => 'Penerima Dummy ' . $i,
            'nama_penyetor' => 'Seeder ' . $i,
            'hp_penyetor' => '08123456789',
            'alamat_penyetor' => 'Jl. Dummy Transfer ' . $i
        ]);
        $controller->storeTransfer($req);
        echo "✅ Transfer $i Saved ($bank).\n";
    }
    catch (\Exception $e) {
        echo "❌ Transfer $i Failed: " . $e->getMessage() . "\n";
    }
}

echo "\n--- SEEDING COMPLETE ---\n";
