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
        // 1. Fetch Settings
        $videoSetting = Setting::where('jenis_set', 'Video')->first();
        $videoFile = $videoSetting ? $videoSetting->value : 'default.mp4';

        $textSetting = Setting::where('jenis_set', 'Text')->first();
        $runningText = $textSetting ? $textSetting->value : 'Selamat Datang di BPRS HIK MCI';

        $sourceSetting = Setting::where('jenis_set', 'MediaSource')->first();
        $mediaSource = $sourceSetting ? $sourceSetting->value : 'local';

        $ytSetting = Setting::where('jenis_set', 'YoutubeUrl')->first();
        $rawYoutubeUrl = $ytSetting ? $ytSetting->value : '';

        // 2. Process YouTube URL to Embed URL
        $embedUrl = '';
        if ($rawYoutubeUrl) {
            // Check for Playlist
            if (preg_match('/list=([^&]+)/', $rawYoutubeUrl, $matches)) {
                $playlistId = $matches[1];
                $embedUrl = "https://www.youtube-nocookie.com/embed?listType=playlist&list={$playlistId}&autoplay=1&mute=1&loop=1&controls=0&showinfo=0";
            }
            else {
                // Single Video
                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $rawYoutubeUrl, $match);
                $youtubeId = $match[1] ?? '';
                $embedUrl = $youtubeId ? "https://www.youtube-nocookie.com/embed/{$youtubeId}?autoplay=1&mute=1&loop=1&playlist={$youtubeId}&controls=0&showinfo=0" : '';
            }
        }

        return view('display.tv', compact('videoFile', 'runningText', 'mediaSource', 'embedUrl'));
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
