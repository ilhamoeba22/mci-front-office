<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DisplayController extends Controller
{
    /**
     * Halaman TV Display (Pengganti tampil_antrian.php)
     */
    public function index()
    {
        // Ambil Nama Video dari Database
        $videoSetting = Setting::where('jenis_set', 'Video')->first();
        $videoFile = $videoSetting ? $videoSetting->value : 'default.mp4'; // Fallback

        return view('display.tv', compact('videoFile'));
    }

    /**
     * API Endpoint untuk mengambil antrian yang sedang dipanggil (AJAX)
     * (Pengganti get_antrian.php)
     */
    public function getQueueData()
    {
        $today = Carbon::now()->format('Y-m-d');

        // 1. Ambil Antrian CS yang sedang DIPANGGIL (st=2)
        $csQueue = Queue::where('type', 'CS')
                        ->where('tgl_antri', $today)
                        ->where('st_antrian', '2')
                        ->orderBy('updated_at', 'desc') // Yang baru dipanggil
                        ->first();
        
        // 2. Jika tidak ada yang dipanggil, ambil antrian terakhir yang SELESAI (st=3)
        //    (Ini logic legacy get_antrian.php: if !found st=2, check st=3)
        if (!$csQueue) {
            $csQueue = Queue::where('type', 'CS')
                            ->where('tgl_antri', $today)
                            ->where('st_antrian', '3')
                            ->orderBy('updated_at', 'desc')
                            ->first();
        }

        // 3. Lakukan hal yang sama untuk Teller
        $tlQueue = Queue::where('type', 'Teller')
                        ->where('tgl_antri', $today)
                        ->where('st_antrian', '2')
                        ->orderBy('updated_at', 'desc')
                        ->first();

        if (!$tlQueue) {
            $tlQueue = Queue::where('type', 'Teller')
                            ->where('tgl_antri', $today)
                            ->where('st_antrian', '3')
                            ->orderBy('updated_at', 'desc')
                            ->first();
        }

        // Format data JSON response
        $data = [
            'cs_antri' => $csQueue ? $csQueue->antrian : 'CS-00',
            'tl_antri' => $tlQueue ? $tlQueue->antrian : 'TL-00',
        ];

        return response()->json($data);
    }
}
