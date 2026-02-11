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
            'type' => 'required|in:deposit,withdrawal,transfer'
        ], [
            'no_rek.required' => 'Nomor rekening wajib diisi.',
            'no_rek.numeric' => 'Nomor rekening harus berupa angka.',
            'type.required' => 'Tipe transaksi tidak valid.',
            'type.in' => 'Tipe transaksi tidak dikenali.'
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
                'alamat' => isset($response['detailAccount']) ? 
                ($response['detailAccount']['address'] . ', ' .
                $response['detailAccount']['subdistrict'] . ', ' .
                $response['detailAccount']['district'] . ', ' .
                $response['detailAccount']['city']) : '-',
                'hp' => $response['detailAccount']['phoneNo'] ?? '-',
                'noid' => $response['detailAccount']['governmentIdNo'] ?? '-',
            ];

            if ($type == 'deposit') {
                return view('transaction.deposit', compact('data'));
            }
            elseif ($type == 'withdrawal') {
                return view('transaction.withdrawal', compact('data'));
            }
            elseif ($type == 'transfer') {
                // PRG Pattern: Store data in session and redirect to GET route
                session(['transfer_data' => $data]);
                return redirect()->route('transaction.transfer.form');
            }
        }
        else {
            // Jika Gagal
            $msg = $response['message'] ?? 'Rekening tidak ditemukan atau terjadi kesalahan API.';
            return back()->withErrors(['no_rek' => $msg])->withInput();
        }
    }

    /**
     * Menampilkan Form Transfer (Step 2) via GET
     * Aman dari error 405 saat redirect back() validation
     */
    public function showTransferForm()
    {
        $data = session('transfer_data');

        // Jika tidak ada data sesi (akses langsung url), kembalikan ke awal
        if (!$data) {
            return redirect()->route('transaction.transfer.create')->withErrors(['msg' => 'Sesi transaksi telah berakhir. Silakan ulangi cek rekening.']);
        }

        $banks = Bank::all();
        return view('transaction.transfer', compact('data', 'banks'));
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
            'nominal' => 'required|numeric|min:10000',
            'nama_penyetor' => 'required',
            'hp_penyetor' => 'required',
            'noid_penyetor' => 'required',
            'alamat_penyetor' => 'required',
        ], [
            'nama.required' => 'Nama pemilik rekening wajib ada.',
            'no_rek.required' => 'Nomor rekening wajib ada.',
            'nominal.required' => 'Nominal setoran wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Minimal setoran adalah Rp 10.000.',
            'nama_penyetor.required' => 'Nama penyetor wajib diisi.',
            'hp_penyetor.required' => 'Nomor HP penyetor wajib diisi.',
            'noid_penyetor.required' => 'Nomor Identitas penyetor wajib diisi.',
            'alamat_penyetor.required' => 'Alamat penyetor wajib diisi.',
        ]);

        try {
            // Generate Token ONCE to ensure consistency between DB and Redirect
            $today = Carbon::now()->format('Y-m-d');
            $token = "ST-" . Carbon::now()->format('ymdHis') . rand(100, 999);

            DB::transaction(function () use ($request, $token, $today) {
                // 1. Simpan Transaksi Setoran
                Transaction::create([
                    'token' => $token,
                    'nama' => $request->nama,
                    'no_rek' => $request->no_rek,
                    'tgl' => $request->input('tgl', $today),
                    'nominal' => $request->nominal,
                    'terbilang' => $request->terbilang ?? '-',
                    'jenis_rekening' => $request->jenis_rekening, // Save Category
                    'berita' => $request->tujuan ?? '-',
                    'tujuan' => $request->tujuan ?? '-',
                    'nama_penyetor' => $request->nama_penyetor,
                    'hp_penyetor' => $request->hp_penyetor,
                    'noid_penyetor' => $request->noid_penyetor,
                    'alamat_penyetor' => $request->alamat_penyetor,
                ]);

                // 2. Generate Antrian Teller
                $this->createTellerQueue($request->nama, $token, $today);
            });

            return redirect()->route('queue.show', ['token' => $token])->with('success', 'Setoran berhasil diproses.');

        }
        catch (\Exception $e) {
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
            'nominal' => 'required|numeric|min:10000',
            'nama_penarik' => 'required',
            'hp_penarik' => 'required',
            'noid_penarik' => 'required',
            'alamat_penarik' => 'required',
        ], [
            'nama.required' => 'Nama pemilik rekening wajib ada.',
            'no_rek.required' => 'Nomor rekening wajib ada.',
            'nominal.required' => 'Nominal penarikan wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Minimal penarikan adalah Rp 10.000.',
            'nama_penarik.required' => 'Nama penarik wajib diisi.',
            'hp_penarik.required' => 'Nomor HP penarik wajib ada.',
            'noid_penarik.required' => 'Nomor Identitas penarik wajib ada.',
            'alamat_penarik.required' => 'Alamat penarik wajib ada.',
        ]);

        try {
            // BACKEND BALANCE VALIDATION
            $response = $this->coreBank->getBalance($request->no_rek);
            if (!isset($response['accountNo'])) {
                return back()->withErrors(['msg' => 'Gagal memverifikasi saldo. Silakan coba lagi.'])->withInput();
            }

            $availableBalance = $response['accountInfo'][0]['availableBalance']['value'] ?? 0;
            if ($request->nominal > $availableBalance) {
                return back()->withErrors(['nominal_display' => 'Saldo tidak mencukupi untuk melakukan penarikan ini.'])->withInput();
            }

            // Generate Token ONCE
            $today = Carbon::now()->format('Y-m-d');
            $token = "TT-" . Carbon::now()->format('ymdHis') . rand(100, 999);

            DB::transaction(function () use ($request, $token, $today) {
                // 1. Simpan Transaksi Penarikan
                Withdrawal::create([
                    'token' => $token,
                    'nama' => $request->nama,
                    'no_rek' => $request->no_rek,
                    'tgl' => $request->input('tgl', $today),
                    'nominal' => $request->nominal,
                    'terbilang' => $request->terbilang ?? '-',
                    'jenis_rekening' => $request->jenis_rekening,
                    'tujuan' => $request->tujuan ?? '-',
                    'nama_penarik' => $request->nama_penarik,
                    'hp_penarik' => $request->hp_penarik,
                    'noid_penarik' => $request->noid_penarik,
                    'alamat_penarik' => $request->alamat_penarik,
                ]);

                // 2. Generate Antrian Teller
                $this->createTellerQueue($request->nama, $token, $today);
            });

            return redirect()->route('queue.show', ['token' => $token])->with('success', 'Penarikan berhasil diproses.');

        }
        catch (\Exception $e) {
            Log::error('Kesalahan Tarik Tunai: ' . $e->getMessage());
            return back()->withErrors(['msg' => 'Terjadi kesalahan saat memproses transaksi.']);
        }
    }

    /**
     * Memproses penyimpanan transaksi Transfer Online.
     */
    public function storeTransfer(Request $request)
    {
        // DEBUG: Cek apakah request masuk
        // dd($request->all());

        $request->validate([
            'nama' => 'required',
            'no_rek' => 'required',
            'nominal' => 'required|numeric|min:10000',
            'bank_tujuan' => 'required',
            'no_rek_tujuan' => 'required',
            'nama_tujuan' => 'required',
            'nama_penyetor' => 'required',
            'hp_penyetor' => 'required',
            'alamat_penyetor' => 'required',
        ], [
            'nama.required' => 'Nama pengirim wajib ada.',
            'no_rek.required' => 'Nomor rekening pengirim wajib ada.',
            'nominal.required' => 'Nominal transfer wajib diisi.',
            'nominal.numeric' => 'Nominal harus berupa angka.',
            'nominal.min' => 'Minimal transfer adalah Rp 10.000.',
            'bank_tujuan.required' => 'Bank tujuan wajib dipilih.',
            'no_rek_tujuan.required' => 'Nomor rekening tujuan wajib diisi.',
            'nama_tujuan.required' => 'Nama penerima wajib diisi.',
            'nama_penyetor.required' => 'Nama pengirim wajib diisi.',
            'hp_penyetor.required' => 'Nomor HP pengirim wajib diisi.',
            'alamat_penyetor.required' => 'Alamat pengirim wajib diisi.',
        ]);

        try {
            // Explode Bank Tujuan (Nama/Biaya) to get fee for balance check
            $bankParts = explode('/', $request->bank_tujuan);
            $bankFee = $bankParts[1] ?? 0;
            $totalDebet = $request->nominal + $bankFee;

            // BACKEND BALANCE VALIDATION
            $response = $this->coreBank->getBalance($request->no_rek);
            if (!isset($response['accountNo'])) {
                return back()->withErrors(['msg' => 'Gagal memverifikasi saldo. Silakan coba lagi.'])->withInput();
            }

            $availableBalance = $response['accountInfo'][0]['availableBalance']['value'] ?? 0;
            if ($totalDebet > $availableBalance) {
                return back()->withErrors(['nominal_display' => 'Saldo tidak mencukupi (Nominal + Biaya Admin: Rp ' . number_format($totalDebet, 0, ',', '.') . ').'])->withInput();
            }

            // Generate Token ONCE
            $today = Carbon::now()->format('Y-m-d');
            $token = "ON-" . Carbon::now()->format('ymdHis') . rand(100, 999);

            DB::transaction(function () use ($request, $token, $today) {
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
                    'tujuan' => $request->tujuan ?? '-',
                    'nama_penyetor' => $request->nama_penyetor,
                    'hp_penyetor' => $request->hp_penyetor,
                    'alamat_penyetor' => $request->alamat_penyetor, // Standardized

                    'nama_tujuan' => $request->nama_tujuan,
                    'no_rek_tujuan' => $request->no_rek_tujuan,
                    'bank_tujuan' => $bankName,
                    'kode_bank' => '-', // Fix: Default value
                    'biaya_trf' => $bankFee,
                    // 'berita_tujuan' removed
                    'negara_tujuan' => $request->negara_tujuan ?? 'Indonesia', // NEW
                    'jenis_trf' => 'ONLINE',
                    'hp_penerima' => $request->hp_penerima ?? '0',
                    'alamat_tujuan' => '-',
                    'kota_tujuan' => '-',
                ]);

                // 2. Generate Antrian Teller
                $this->createTellerQueue($request->nama, $token, $today);
            });

            return redirect()->route('queue.show', ['token' => $token])->with('success', 'Transfer berhasil diproses.');

        }
        catch (\Exception $e) {
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
