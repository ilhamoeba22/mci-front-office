<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QueueController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\DisplayController;

/*
|--------------------------------------------------------------------------
| Web Routes (Rute Web)
|--------------------------------------------------------------------------
|
| Di sini Anda dapat mendaftarkan rute web untuk aplikasi Anda.
| Rute-rute ini dimuat oleh RouteServiceProvider dan ditugaskan ke grup 
| middleware "web".
|
*/

// Halaman Utama (Dashboard Menu)
Route::get('/', function () {
    return view('home');
});

// Rute Antrian (Public)
Route::post('/queue', [QueueController::class, 'store'])->name('queue.store');
Route::get('/queue/{token}', [QueueController::class, 'show'])->name('queue.show');

// Rute Transaksi
Route::post('/transaction/check', [TransactionController::class, 'checkAccount'])->name('transaction.check');
Route::get('/transaction/deposit', [TransactionController::class, 'createDeposit'])->name('transaction.deposit.create');
Route::post('/transaction/deposit/store', [TransactionController::class, 'storeDeposit'])->name('transaction.deposit.store');
Route::get('/transaction/withdrawal', [TransactionController::class, 'createWithdrawal'])->name('transaction.withdrawal.create');
Route::post('/transaction/withdrawal/store', [TransactionController::class, 'storeWithdrawal'])->name('transaction.withdrawal.store');
Route::get('/transaction/transfer', [TransactionController::class, 'createTransfer'])->name('transaction.transfer.create');
Route::post('/transaction/transfer/store', [TransactionController::class, 'storeTransfer'])->name('transaction.transfer.store');

// Rute Admin (Back Office)
Route::prefix('admin')->name('admin.')->group(function () {
    // Dashboard Utama
    Route::get('/', [AdminController::class, 'index'])->name('dashboard');
    
    // Manajemen Antrian (Call/Done)
    Route::get('/queue', [AdminController::class, 'queueList'])->name('queue.list');
    Route::post('/queue/call/{id}', [AdminController::class, 'callQueue'])->name('queue.call');
    Route::post('/queue/finish/{id}', [AdminController::class, 'finishQueue'])->name('queue.finish');
    
    // Settings (Media)
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings/media', [AdminController::class, 'updateMedia'])->name('settings.media.update');
});

// Rute TV Display
Route::get('/display', [DisplayController::class, 'index'])->name('display.index');
Route::get('/api/queue-data', [DisplayController::class, 'getQueueData'])->name('api.queue.data');

// DEBUG API ROUTE (Temporary)
Route::get('/debug-api', function(App\Services\CoreBankingService $service) {
    try {
        echo "<h1>API DEBUGGER</h1>";
        echo "<pre>";
        
        echo "1. TEST CONFIG\n";
        echo "Base URL: " . config('services.core_bank.base_url') . "\n";
        echo "Client ID: " . config('services.core_bank.client_id') . "\n";
        
        echo "\n2. REQUESTING TOKEN...\n";
        $tokenData = $service->getToken();
        
        if ($tokenData) {
            echo "SUCCESS! Token received.\n";
            print_r($tokenData);
            
            echo "\n3. CHECKING BALANCE (Test Account: 0011223344)...\n";
            $balance = $service->getBalance('0011223344');
            print_r($balance);
        } else {
            echo "FAILED to get Token.\n";
            echo "Check laravel.log for details.\n";
        }
        
        echo "</pre>";
    } catch (\Exception $e) {
        echo "EXCEPTION: " . $e->getMessage();
    }
});
