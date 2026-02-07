<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use Illuminate\Http\Request;
use Carbon\Carbon;

/**
 * Class QueueController
 * 
 * Mengelola logika pengambilan antrian (Ticketing).
 */
class QueueController extends Controller
{
    /**
     * Menyimpan antrian baru ke database.
     * Digunakan untuk Antrian CS maupun Teller (Via Setor Tunai).
     */
    public function store(Request $request)
    {
        $type = $request->input('type', 'CS'); // Default tipe CS
        $today = Carbon::now()->format('Y-m-d');

        // Logika meniru sistem lama (cskeun.php):
        // 1. Hitung jumlah antrian hari ini berdasarkan tipe
        $count = Queue::where('type', $type)
            ->where('tgl_antri', $today)
            ->count();

        $next = $count + 1;
        // Prefix kodifikasi: CS-XX atau TL-XX
        $prefix = ($type == 'CS') ? 'CS-' : 'TL-';
        $queueNumber = $prefix . str_pad($next, 2, '0', STR_PAD_LEFT);

        // Generate Token Unik untuk URL
        $token = $prefix . Carbon::now()->format('ymdHis');

        // 2. Simpan ke Database
        try {
            $queue = Queue::create([
                'tgl_antri' => $today,
                'nama_antrian' => $type . ' Queue', // Nama default jika tidak ada input spesifik
                'type' => $type,
                'antrian' => $queueNumber,
                'kode' => $token,
                'st_antrian' => '0' // Status awal 0 (Belum dipanggil)
            ]);
        }
        catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Queue Store Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan antrian'], 500);
        }

        // Arahkan ke halaman cetak tiket
        return redirect()->route('queue.show', ['token' => $token]);
    }

    /**
     * Menampilkan detail tiket berdasarkan token.
     * Halaman ini bisa dicetak oleh nasabah.
     */
    public function show($token)
    {
        $queue = Queue::where('kode', $token)->firstOrFail();
        return view('queue.ticket', compact('queue'));
    }
}
