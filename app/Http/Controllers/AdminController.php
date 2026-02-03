<?php

namespace App\Http\Controllers;

use App\Models\Queue;
use App\Models\Setting;
use App\Models\Transaction; // Untuk report sederhana
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Dashboard Utama (Pengganti aiueo.php)
     * Menampilkan menu akses ke list antrian, report, dan setting.
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Menampilkan List Antrian (Pengganti list.php)
     * Bisa filter berdasarkan jenis: 'CS' atau 'Teller'
     */
    public function queueList(Request $request)
    {
        $type = $request->query('type', 'CS'); // Default CS
        $today = Carbon::now()->format('Y-m-d');

        // Ambil antrian hari ini yang belum selesai (st_antrian != 3)
        // st_antrian: 0=Menunggu, 1=Aktif?, 2=Dipanggil, 3=Selesai
        // Asumsi logic legacy: list.php menampilkan yang status 0, 1, 2
        
        $queues = Queue::where('type', $type)
                       ->whereDate('tgl_antri', $today)
                       ->where('st_antrian', '!=', '3') 
                       ->orderBy('id_antrian', 'asc')
                       ->get();

        return view('admin.queue_list', compact('queues', 'type'));
    }

    /**
     * Memanggil Antrian (Call)
     * Mengubah status menjadi '2' (Dipanggil/Call)
     */
    public function callQueue($id)
    {
        // 1. Reset status '2' antrian lain di tipe yang sama (agar tidak ada 2 yang dipanggil bersamaan)
        // Logic legacy tidak explicit reset, tapi biasanya begitu. Kita check dulu.
        $queue = Queue::findOrFail($id);
        
        // Update status diri sendiri jadi 2
        $queue->st_antrian = '2'; 
        $queue->save();
        
        // Optional: Update antrian sebelumnya jadi selesai (3) jika flow otomatis?
        // Untuk sekarang manual click "Selesai" saja biar aman.

        return back()->with('success', 'Antrian ' . $queue->antrian . ' dipanggil.');
    }

    /**
     * Menyelesaikan Antrian (Done)
     * Mengubah status menjadi '3' (Selesai)
     */
    public function finishQueue($id)
    {
        $queue = Queue::findOrFail($id);
        $queue->st_antrian = '3';
        $queue->save();

        return back()->with('success', 'Antrian ' . $queue->antrian . ' selesai.');
    }

    /**
     * Halaman Setting Media & Text (Pengganti setting_view.php)
     */
    public function settings()
    {
        $video = Setting::where('jenis_set', 'Video')->first();
        $text = Setting::where('jenis_set', 'Text')->first(); // If needed for running text

        // Rename variable to prevent conflict
        $settingsView = true;

        return view('admin.settings', compact('video', 'text', 'settingsView'));
    }

    /**
     * Proses Upload Video
     */
    public function updateMedia(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,ogg|max:20000', // Max 20MB
        ]);

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            // Simpan ke public/assets/media (sesuai legacy path atau baru)
            $file->move(public_path('assets/media'), $filename);

            // Update DB
            Setting::updateOrCreate(
                ['jenis_set' => 'Video'],
                ['value' => $filename]
            );

            return back()->with('success', 'Video berhasil diperbarui.');
        }

        return back()->with('error', 'Gagal upload video.');
    }
}
