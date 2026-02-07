<?php
// Simplified 5-month seeder
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Carbon\Carbon;
use Faker\Factory;

$faker = Factory::create('id_ID');
$start = Carbon::now()->subMonths(5)->startOfMonth();
$end = Carbon::now();
$days = $start->diffInDays($end);

echo "Creating 1500 uptrend transactions over 5 months...\n";

// Base nominal for uptrend calculation
$baseNominal = 500000;
$dailyGrowthRate = 1.015; // 1.5% daily growth for smooth uptrend

for ($i = 0; $i < 1500; $i++) {
    $dayOffset = rand(0, $days);
    $date = $start->copy()->addDays($dayOffset)->setTime(rand(8, 15), rand(0, 59), rand(0, 59));
    $type = ['setor', 'tarik', 'transfer'][rand(0, 2)];
    $prefix = $type == 'setor' ? 'ST-' : ($type == 'tarik' ? 'TT-' : 'ON-');
    $token = $prefix . strtoupper(uniqid());

    // Calculate uptrend nominal with controlled variance
    $trendNominal = $baseNominal * pow($dailyGrowthRate, $dayOffset);

    // Add random variance: ±8% (reduced from ±10% for tighter candles)
    $variance = (rand(-800, 800) / 10000); // -0.08 to +0.08

    // 15% chance of pullback (simulate minor corrections)
    if (rand(1, 100) <= 15) {
        $variance = (rand(-500, -100) / 10000); // -0.05 to -0.01 (pullback)
    }

    $nominal = round($trendNominal * (1 + $variance), -3); // Round to nearest 1000

    // Ensure minimum and maximum bounds
    $nominal = max(100000, min(50000000, $nominal));

    $data = [
        'token' => $token,
        'nama' => $faker->name(),
        'no_rek' => $faker->numerify('##########'),
        'tgl' => $date->format('Y-m-d'),
        'nominal' => $nominal,
        'terbilang' => 'Rupiah',
        'created' => $date
    ];

    if ($type == 'setor') {
        \App\Models\Transaction::create(array_merge($data, [
            'jenis_rekening' => 'Tabungan',
            'berita' => 'Setor',
            'tujuan' => 'Tabung',
            'nama_penyetor' => $faker->name(),
            'hp_penyetor' => '081234567890',
            'noid_penyetor' => '1234567890123456',
            'alamat_penyetor' => $faker->address()
        ]));
    }
    elseif ($type == 'tarik') {
        \App\Models\Withdrawal::create(array_merge($data, [
            'jenis_rekening' => 'Tabungan',
            'tujuan' => 'Tarik Tunai',
            'nama_penarik' => $faker->name(),
            'hp_penarik' => '081234567890',
            'noid_penarik' => '1234567890123456',
            'alamat_penarik' => $faker->address()
        ]));
    }
    else {
        \App\Models\Transfer::create(array_merge($data, [
            'tujuan' => 'Transfer',
            'nama_penyetor' => $faker->name(),
            'hp_penyetor' => '081234567890',
            'alamat_penyetor' => $faker->address(),
            'nama_tujuan' => $faker->name(),
            'no_rek_tujuan' => '9876543210',
            'bank_tujuan' => 'BCA',
            'kode_bank' => '014',
            'alamat_tujuan' => $faker->address(),
            'kota_tujuan' => $faker->city(),
            'biaya_trf' => 6500,
            'jenis_trf' => 'ONLINE',
            'hp_penerima' => '081234567890'
        ]));
    }

    \App\Models\Queue::create([
        'tgl_antri' => $date->format('Y-m-d'),
        'nama_antrian' => $faker->name(),
        'type' => $type == 'transfer' ? 'CS' : 'Teller',
        'antrian' => ($type == 'transfer' ? 'C' : 'T') . sprintf('%03d', ($i % 999) + 1),
        'kode' => $token,
        'st_antrian' => 3,
        'tujuan_datang' => ucfirst($type),
        'solusi' => 'Selesai',
        'nama' => $data['nama'],
        'no_kontak' => '081234567890',
        'waktu_panggil' => $date->copy()->addMinutes(10),
        'created' => $date,
        'updated_at' => $date->copy()->addMinutes(30)
    ]);

    if (($i + 1) % 100 == 0)
        echo($i + 1) . " done\n";
}

echo "\nComplete!\n";
echo "Setor: " . \App\Models\Transaction::count() . "\n";
echo "Tarik: " . \App\Models\Withdrawal::count() . "\n";
echo "Transfer: " . \App\Models\Transfer::count() . "\n";
