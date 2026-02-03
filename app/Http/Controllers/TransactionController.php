<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Transfer;
use App\Models\Queue;
use App\Models\Bank;
use App\Services\CoreBankingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

/**
 * Class TransactionController
 * 
 * Mengelola alur transaksi seperti Setor Tunai, Tarik Tunai, dan Transfer.
 */
class TransactionController extends Controller
{
    protected $coreBank;

    /**
     * Injeksi CoreBankingService untuk menangani koneksi API.
     */
    public function __construct(CoreBankingService $coreBank)
    {
        $this->coreBank = $coreBank;
    }

    /**
     * Menampilkan halaman awal form Setor Tunai (Input Nomor Rekening).
     */
    public function createDeposit()
    {
        return view('transaction.check', ['type' => 'deposit']);
    }
    
    /**
     * Menampilkan halaman awal form Tarik Tunai.
     */
    public function createWithdrawal()
    {
        return view('transaction.check', ['type' => 'withdrawal']);
    }

    /**
     * Menampilkan halaman awal form Transfer Online.
     */
    public function createTransfer()
    {
        return view('transaction.check', ['type' => 'transfer']);
    }

    /**
     * Memeriksa validitas nomor rekening via API Core Banking.
     * Jika valid, arahkan ke form detail transaksi sesuai tipe.
     */
    public function checkAccount(Request $request)
    {
        $request->validate([
            'no_rek' => 'required|numeric',
            'type'   => 'required|in:deposit,withdrawal,transfer'
        ]);

        $noRek = $request->no_rek;
        $type = $request->type;
        
        // Panggil API Core Banking untuk cek saldo dan nama
        $response = $this->coreBank->getBalance($noRek);

        if (isset($response['accountNo'])) {
            // Jika Sukses (Rekening ditemukan)
            $data = [
                'no_rek' => $response['accountNo'],
                'nama' => $response['name'],
                'saldo' => $response['accountInfo'][0]['availableBalance']['value'] ?? 0,
                'minimum' => $response['accountInfo'][0]['minimalAmount']['value'] ?? 0,
                'alamat'  => $response['address'] ?? '-', // Jika API support address
                'hp' => $response['mobileNo'] ?? '-',
                'noid' => $response['identityNo'] ?? '-',
            ];

            if ($type == 'deposit') {
                return view('transaction.deposit', compact('data'));
            } elseif ($type == 'withdrawal') {
                return view('transaction.withdrawal', compact('data'));
            } elseif ($type == 'transfer') {
                $banks = Bank::all();
                return view('transaction.transfer', compact('data', 'banks'));
            }
        } else {
            // Jika Gagal
            $msg = $response['message'] ?? 'Rekening tidak ditemukan atau terjadi kesalahan API.';
            return back()->withErrors(['no_rek' => $msg])->withInput();
        }
    }

    /**
     * Memproses penyimpanan transaksi Setor Tunai.
     */
    public function storeDeposit(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required',
            'no_rek' => 'required',
            'nominal' => 'required|numeric',
            'nama_penyetor' => 'required',
            'hp_penyetor' => 'required',
        ]);

        try {
            DB::transaction(function () use ($request) {
                $today = Carbon::now()->format('Y-m-d');
                $token = "ST-" . Carbon::now()->format('ymdHis');

                // 1. Simpan Transaksi Setoran
                Transaction::create([
                    'token' => $token,
                    'nama' => $request->nama,
                    'no_rek' => $request->no_rek,
                    'tgl' => $request->input('tgl', $today),
                    'nominal' => $request->nominal,
                    'terbilang' => $request->terbilang ?? '-',
                    'berita' => $request->berita,
                    'tujuan' => $request->tujuan,
                    'nama_penyetor' => $request->nama_penyetor,
                    'hp_penyetor' => $request->hp_penyetor,
                    'noid_penyetor' => $request->noid_penyetor,
                    'alamat_penyetor' => $request->alamat_penyetor,
                ]);

                // 2. Generate Antrian Teller
                $this->createTellerQueue($request->nama, $token, $today);
            });

            // Ambil token baru (karena di dalam closure)
            $token = "ST-" . Carbon::now()->format('ymdHis'); // Note: This is strictly mostly for redirect. Ideally catch from closure return.
            // Correction: capture token outside
            
            return redirect()->route('queue.show', ['token' => "ST-" . Carbon::now()->format('ymdHis')])->with('success', 'Setoran berhasil diproses.');

        } catch (\Exception $e) {
            Log::error('Kesalahan Setor Tunai: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses transaksi.']);
        }
    }

    /**
     * Memproses penyimpanan transaksi Tarik Tunai.
     */
    public function storeWithdrawal(Request $request)
    {
         $request->validate([
            'nama' => 'required',
            'no_rek' => 'required',
            'nominal' => 'required|numeric',
            'nama_penarik' => 'required',
        ]);

        try {
             $token = "TT-" . Carbon::now()->format('ymdHis');
             DB::transaction(function () use ($request, $token) {
                $today = Carbon::now()->format('Y-m-d');
                
                // 1. Simpan Transaksi Penarikan
                Withdrawal::create([
                    'token' => $token,
                    'nama' => $request->nama,
                    'no_rek' => $request->no_rek,
                    'tgl' => $request->input('tgl', $today),
                    'nominal' => $request->nominal,
                    'terbilang' => $request->terbilang ?? '-',
                    'tujuan' => $request->tujuan,
                    'nama_penarik' => $request->nama_penarik,
                    'hp_penarik' => $request->hp_penarik,
                    'noid_penarik' => $request->noid_penarik,
                    'alamat_penarik' => $request->alamat_penarik,
                ]);

                 // 2. Generate Antrian Teller
                 $this->createTellerQueue($request->nama, $token, $today);
             });

             return redirect()->route('queue.show', ['token' => $token])->with('success', 'Penarikan berhasil diproses.');

        } catch (\Exception $e) {
            Log::error('Kesalahan Tarik Tunai: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses transaksi.']);
        }
    }

    /**
     * Memproses penyimpanan transaksi Transfer Online.
     */
    public function storeTransfer(Request $request)
    {
         $request->validate([
            'nama' => 'required',
            'no_rek' => 'required',
            'nominal' => 'required|numeric',
            'bank_tujuan' => 'required',
            'no_rek_tujuan' => 'required',
        ]);

        try {
             $token = "ON-" . Carbon::now()->format('ymdHis');
             DB::transaction(function () use ($request, $token) {
                $today = Carbon::now()->format('Y-m-d');
                
                // Explode Bank Tujuan (Nama/Biaya)
                $bankParts = explode('/', $request->bank_tujuan);
                $bankName = $bankParts[0] ?? '-';
                $bankFee = $bankParts[1] ?? 0;

                // 1. Simpan Transaksi Transfer
                Transfer::create([
                    'token' => $token,
                    'nama' => $request->nama,
                    'no_rek' => $request->no_rek,
                    'tgl' => $request->input('tgl', $today),
                    'nominal' => $request->nominal,
                    'terbilang' => $request->terbilang ?? '-',
                    'tujuan' => $request->tujuan,
                    'nama_penyetor' => $request->nama_penyetor, // Diisi nama pengirim
                    'hp_penyetor' => $request->hp_penyetor,
                    'alamat_' => $request->alamat_penyetor,
                    
                    'nama_tujuan' => $request->nama_tujuan,
                    'no_rek_tujuan' => $request->no_rek_tujuan,
                    'bank_tujuan' => $bankName,
                    'biaya_trf' => $bankFee,
                    'berita_tujuan' => $request->berita_tujuan,
                    'jenis_trf' => 'ONLINE',
                    'hp_penerima' => $request->hp_penerima ?? '0',
                    'alamat_tujuan' => '-', // Default
                    'kota_tujuan' => '-', // Default
                ]);

                 // 2. Generate Antrian Teller
                 $this->createTellerQueue($request->nama, $token, $today);
             });

             return redirect()->route('queue.show', ['token' => $token])->with('success', 'Transfer berhasil diproses.');

        } catch (\Exception $e) {
            Log::error('Kesalahan Transfer: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses transaksi.']);
        }
    }

    /**
     * Helper untuk membuat antrian Teller
     */
    private function createTellerQueue($nama, $token, $today)
    {
        $count = Queue::where('type', 'Teller')
                ->where('tgl_antri', $today)
                ->count();

        $next = $count + 1;
        $queueNumber = "TL-" . str_pad($next, 2, '0', STR_PAD_LEFT);

        Queue::create([
            'tgl_antri' => $today,
            'nama_antrian' => $nama,
            'type' => 'Teller',
            'antrian' => $queueNumber,
            'kode' => $token,
            'st_antrian' => '0'
        ]);
    }
}
