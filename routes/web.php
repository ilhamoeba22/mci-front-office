<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\TransactionPrintController;
use App\Http\Controllers\AuthController;

/* |-------------------------------------------------------------------------- | Web Routes (Rute Web) |-------------------------------------------------------------------------- | | Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda. | Rute-rute ini dimuat oleh RouteServiceProvider dan ditugaskan ke grup  | middleware "web". | */

// Halaman Utama (Dashboard Menu)

// --- DEBUG & UTILITY ROUTES (DISABLED FOR PRODUCTION) ---
/*
Route::get('/migrate-db', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migration Success: ' . Artisan::output();
    }
    catch (\Exception $e) {
        return 'Migration Failed: ' . $e->getMessage();
    }
});

Route::get('/fix-schema', function () {
    try {
        $output = "Checking schema...\n";

        if (!\Illuminate\Support\Facades\Schema::hasColumn('tbl_antrian', 'nama')) {
            \Illuminate\Support\Facades\Schema::table('tbl_antrian', function (Illuminate\Database\Schema\Blueprint $table) {
                            $table->string('nama', 100)->nullable()->after('nama_antrian');
                        }
                        );
                        $output .= "Added column 'nama'.\n";
                    }
                    else {
                        $output .= "Column 'nama' already exists.\n";
                    }

                    if (!\Illuminate\Support\Facades\Schema::hasColumn('tbl_antrian', 'no_kontak')) {
                        \Illuminate\Support\Facades\Schema::table('tbl_antrian', function (Illuminate\Database\Schema\Blueprint $table) {
                            $table->string('no_kontak', 20)->nullable()->after('nama');
                        }
                        );
                        $output .= "Added column 'no_kontak'.\n";
                    }
                    else {
                        $output .= "Column 'no_kontak' already exists.\n";
                    }
                    if (!\Illuminate\Support\Facades\Schema::hasColumn('tbl_antrian', 'updated_at')) {
                        \Illuminate\Support\Facades\Schema::table('tbl_antrian', function (Illuminate\Database\Schema\Blueprint $table) {
                            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
                        }
                        );
                        $output .= "Added column 'updated_at'.\n";
                    }
                    else {
                        $output .= "Column 'updated_at' already exists.\n";
                    }

                    return "Schema Fix Result:\n<pre>$output</pre>";
                }
                catch (\Exception $e) {
                    return "Schema Fix Failed: " . $e->getMessage();
                }
            });

Route::get('/debug-queue/{antrian?}', function ($antrian = null) {
    if ($antrian) {
        $queue = \App\Models\Queue::where('antrian', $antrian)
            ->whereDate('tgl_antri', \Carbon\Carbon::now())
            ->orderBy('id_antrian', 'desc')
            ->first();
    }
    else {
        $queue = \App\Models\Queue::orderBy('id_antrian', 'desc')->first();
    }

    if (!$queue)
        return 'No Queue Found for: ' . ($antrian ?? 'Latest');

    $transaction = null;
    $log = "Queue Code: " . $queue->kode . "\n";

    if ($queue->kode) {
        if (str_starts_with($queue->kode, 'ST-')) {
            $transaction = \App\Models\Transaction::where('token', $queue->kode)->first();
            $log .= "Type: ST (Setor Tunai)\n";
        }
        elseif (str_starts_with($queue->kode, 'TT-')) {
            $transaction = \App\Models\Withdrawal::where('token', $queue->kode)->first();
            $log .= "Type: TT (Tarik Tunai)\n";
        }
        elseif (str_starts_with($queue->kode, 'ON-')) {
            $transaction = \App\Models\Transfer::where('token', $queue->kode)->first();
            $log .= "Type: ON (Transfer Online)\n";
        }
    }

    return [
    'queue' => $queue,
    'transaction' => $transaction,
    'log' => $log
    ];
});

Route::get('/debug-data/{id}', function ($id) {
    try {
        $queue = \App\Models\Queue::find($id);
        if (!$queue)
            return response()->json(['error' => 'Queue not found']);

        // Test the Accessor
        $transaction = $queue->transaction;

        return response()->json([
        'status' => 'ok',
        'queue' => $queue,
        'computed_transaction' => $transaction,
        'raw_kode' => $queue->kode
        ]);
    }
    catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
*/

Route::get('/', function () {
    return view('home');
});

// Authentication Routes
Route::get('/login', [AuthController::class , 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class , 'login'])->name('login.post');
Route::post('/logout', [AuthController::class , 'logout'])->name('logout');

// Rute Antrian (Public)
Route::post('/queue', [QueueController::class , 'store'])->name('queue.store');
Route::get('/queue/{token}', [QueueController::class , 'show'])->name('queue.show');

// Rute Transaksi
Route::post('/transaction/check', [TransactionController::class , 'checkAccount'])->name('transaction.check');
Route::get('/transaction/deposit', [TransactionController::class , 'createDeposit'])->name('transaction.deposit.create');
Route::post('/transaction/deposit/store', [TransactionController::class , 'storeDeposit'])->name('transaction.deposit.store');
Route::get('/transaction/withdrawal', [TransactionController::class , 'createWithdrawal'])->name('transaction.withdrawal.create');
Route::post('/transaction/withdrawal/store', [TransactionController::class , 'storeWithdrawal'])->name('transaction.withdrawal.store');
Route::get('/transaction/transfer', [TransactionController::class , 'createTransfer'])->name('transaction.transfer.create');
Route::get('/transaction/transfer/form', [TransactionController::class , 'showTransferForm'])->name('transaction.transfer.form');
Route::post('/transaction/transfer/store', [TransactionController::class , 'storeTransfer'])->name('transaction.transfer.store');

// Rute Admin (Back Office) - Protected
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard Utama
    Route::get('/', [AdminController::class , 'index'])->name('dashboard');

    // Manajemen Antrian (Call/Done)
    Route::get('/queue', [AdminController::class , 'queueList'])->name('queue.list');
    Route::get('/queue-v2', [AdminController::class , 'queueListV2'])->name('queue.list.v2');
    Route::post('/queue/call/{id}', [AdminController::class , 'callQueue'])->name('queue.call');
    Route::post('/queue/finish/{id}', [AdminController::class , 'finishQueue'])->name('queue.finish');
    Route::post('/queue/skip/{id}', [AdminController::class , 'skipQueue'])->name('queue.skip');
    Route::get('/queue/detail/{id}', [AdminController::class , 'getQueueDetail'])->name('queue.detail');
    Route::get('/queue-history', [AdminController::class , 'queueHistory']);
    Route::get('/queue-history/export', [AdminController::class , 'exportHistory']);


    // Settings (Media)
    Route::get('/settings-data', [AdminController::class , 'settings'])->name('settings.data');
    Route::get('/chart-data', [AdminController::class , 'getChartData'])->name('dashboard.chart'); // NEW
    Route::post('/settings/media', [AdminController::class , 'updateMedia'])->name('settings.media.update');
    Route::post('/settings/text', [AdminController::class , 'updateText'])->name('settings.text.update');

    // High Precision Print Routes (SLIPS)
    Route::get('/print/transfer/{token}', [TransactionPrintController::class, 'printTransfer'])->name('print.transfer');
    Route::get('/print/tarik/{token}', [TransactionPrintController::class, 'printTarik'])->name('print.tarik');
    Route::get('/print/setor/{token}', [TransactionPrintController::class, 'printSetor'])->name('print.setor');
    Route::get('/print/report/{token}', [PrintController::class, 'printReport'])->name('print.report');
    Route::post('/queue/update-transfer/{token}', [AdminController::class, 'updateTransfer'])->name('queue.update-transfer');
    Route::post('/queue/update-withdrawal/{token}', [AdminController::class, 'updateWithdrawal'])->name('queue.update-withdrawal');

    // SPA Catch-all (MUST BE LAST in this group)
    // Allows Vue Router to handle sub-paths like /admin/queue/CS on refresh
    Route::get('/{any}', [AdminController::class , 'index'])->where('any', '.*')->name('spa.fallback');
});

// Rute TV Display
Route::get('/display', [DisplayController::class , 'index'])->name('display.index');
Route::get('/api/queue-data', [DisplayController::class , 'getQueueData'])->name('api.queue.data');

/*
// Route Diagnosa API Vendor
Route::get('/cek-api-vendor', function() {
    try {
        $response = \Illuminate\Support\Facades\Http::withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->timeout(10)
            ->get('https://bprs-mci.mitrasoft.com/mci-api/api/v1/token');
            
        return [
            'status_code' => $response->status(),
            'is_success' => $response->successful(),
            'message' => $response->successful() ? 'Koneksi Tembus ke Vendor!' : 'Koneksi Ditolak/Error oleh Vendor',
            'raw_body' => $response->body()
        ];
    } catch (\Exception $e) {
        return [
            'status' => 'Gagal Koneksi (Timeout/Firewall)',
            'error' => $e->getMessage()
        ];
    }
});

// Route Tes Token Asli (Menggunakan Service)
Route::get('/tes-token', function() {
    try {
        $service = new \App\Services\CoreBankingService();
        $tokenData = $service->getToken();
        
        if ($tokenData && isset($tokenData['accessToken'])) {
            return [
                'status' => 'SUKSES!',
                'message' => 'Aplikasi Berhasil Login & Mendapatkan Token',
                'accessToken_preview' => substr($tokenData['accessToken'], 0, 20) . '...',
                'timestamp_used' => $tokenData['timestamp']
            ];
        } else {
            return [
                'status' => 'KONEKSI TERBUKA TAPI LOGIN GAGAL',
                'message' => 'Cek kembali Client ID, Secret, atau Private Key di .env',
                'raw_response' => 'Lihat storage/logs/laravel.log untuk detailnya'
            ];
        }
    } catch (\Exception $e) {
        return [
            'status' => 'ERROR SISTEM',
            'error' => $e->getMessage()
        ];
    }
});
*/
