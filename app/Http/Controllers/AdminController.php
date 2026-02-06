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
     * Dashboard Utama (SPA Entry Point)
     */
    public function index()
    {
        return view('layouts.admin_spa');
    }

    /**
     * Get Dashboard Stats (API)
     */
    public function getStats()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        return response()->json([
            'cs_total' => Queue::where('type', 'CS')->whereDate('tgl_antri', $today)->count(),
            'cs_waiting' => Queue::where('type', 'CS')->whereDate('tgl_antri', $today)->where('st_antrian', 0)->count(),
            'teller_total' => Queue::where('type', 'Teller')->whereDate('tgl_antri', $today)->count(),
            'teller_waiting' => Queue::where('type', 'Teller')->whereDate('tgl_antri', $today)->where('st_antrian', 0)->count(),
        ]);
    }

    /**
     * Get Queue List (API)
     */
    public function queueList(Request $request)
    {
        $type = $request->query('type', 'CS');
        $today = Carbon::now()->format('Y-m-d');

        $queues = Queue::where('type', $type)
                       ->whereDate('tgl_antri', $today) // Show only today's queues
                       ->orderBy('id_antrian', 'asc')
                       ->get();

        return response()->json($queues);
    }

    /**
     * Get Queue List V2 (Bypass Cache)
     */
    public function queueListV2(Request $request)
    {
        $type = $request->query('type', 'CS');
        $today = Carbon::now()->format('Y-m-d');
        
        // Show only today's queues
        $queues = Queue::where('type', $type)
                       ->whereDate('tgl_antri', $today)
                       ->orderBy('id_antrian', 'asc') // FIFO: Oldest First
                       ->get();

        return response()->json($queues);
    }

    /**
     * Call Queue (API)
     */
    public function callQueue($id)
    {
        $queue = Queue::findOrFail($id);
        
        $queue->st_antrian = '2'; // Status DI PANGGIL
        $queue->waktu_panggil = Carbon::now(); // Record Call Time
        $queue->save();

        return response()->json(['message' => 'Antrian dipanggil', 'data' => $queue]);
    }

    /**
     * Finish Queue (API)
     */
    public function finishQueue(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        
        // Save optional details if provided
        $queue->tujuan_datang = $request->input('tujuan_datang');
        $queue->solusi = $request->input('solusi');
        
        // Save new columns if present in request
        if ($request->has('nama')) {
            $queue->nama = $request->input('nama');
        }
        if ($request->has('no_kontak')) {
            $queue->no_kontak = $request->input('no_kontak');
        }

        // Logic Save Transfer Details (Teller)
        if ($queue->kode && str_starts_with($queue->kode, 'ON-')) {
            $transfer = \App\Models\Transfer::where('token', $queue->kode)->first();
            if ($transfer) {
                // Only update if provided
                if ($request->filled('metode_transfer')) $transfer->metode_transfer = $request->metode_transfer;
                if ($request->filled('mata_uang')) $transfer->mata_uang = $request->mata_uang;
                if ($request->filled('sumber_dana')) $transfer->sumber_dana = $request->sumber_dana;
                $transfer->save();
            }
        }

        $queue->st_antrian = '3'; // Status SELESAI
        $queue->save();

        return response()->json(['message' => 'Antrian selesai', 'data' => $queue]);
    }

    /**
     * Get Queue Detail with Transaction Data (API)
     */
    public function getQueueDetail($id)
    {
        $queue = Queue::findOrFail($id);
        
        // Logic dipindahkan ke Model Queue (Clean Code)
        // Lihat: App\Models\Queue::getTransactionAttribute
        
        return response()->json([
            'queue' => $queue,
            'transaction' => $queue->transaction,
            'tx_type' => $queue->tx_type
        ]);
    }

    /**
     * Skip Queue (API)
     */
    public function skipQueue(Request $request, $id)
    {
        $queue = Queue::findOrFail($id);
        $queue->st_antrian = '4'; // Status LEWATI / BATAL
        
        // Save optional reason if provided
        if ($request->has('solusi')) {
            $queue->solusi = $request->input('solusi');
        }
        
        $queue->save();

        return response()->json(['message' => 'Antrian dilewati', 'data' => $queue]);
    }

    /**
     * Get Queue History with Filters (API)
     */
    public function queueHistory(Request $request)
    {
        $type = $request->query('type', 'CS');
        $date = $request->query('date');
        $search = $request->query('search');

        $query = Queue::where('type', $type)
                      ->whereIn('st_antrian', ['3', '4']); // Only Finished or Skipped

        if ($date) {
             $query->whereDate('tgl_antri', $date);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('antrian', 'like', "%{$search}%")
                  ->orWhere('nama', 'like', "%{$search}%")
                  ->orWhere('kode', 'like', "%{$search}%")
                  // Smart filters for Transaction relationships
                  ->orWhereHas('setor', function($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                           ->orWhere('no_rek', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tarik', function($q2) use ($search) {
                        $q2->where('nama_penarik', 'like', "%{$search}%")
                           ->orWhere('no_rek', 'like', "%{$search}%");
                  })
                  ->orWhereHas('transfer', function($q2) use ($search) {
                        $q2->where('nama', 'like', "%{$search}%")
                           ->orWhere('nama_tujuan', 'like', "%{$search}%")
                           ->orWhere('no_rek', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by Transaction Type (derived from Kode)
        $txType = $request->query('tx_type');
        if ($txType) {
            if ($txType === 'Setor Tunai') {
                $query->where('kode', 'like', 'ST-%');
            } elseif ($txType === 'Tarik Tunai') {
                $query->where('kode', 'like', 'TT-%');
            } elseif ($txType === 'Transfer') {
                $query->where('kode', 'like', 'ON-%');
            }
        }

        // Use pagination (10 items per page)
        $queues = $query->orderBy('id_antrian', 'desc')->paginate(10);

        return response()->json($queues);
    }

    /**
     * Get Settings (API)
     */
    public function settings()
    {
        return response()->json([
            'video' => Setting::where('jenis_set', 'Video')->first(),
            'text' => Setting::where('jenis_set', 'Text')->first(),
        ]);
    }

    /**
     * Update Media (API)
     */
    public function updateMedia(Request $request)
    {
        $request->validate([
            'video' => 'required|mimes:mp4,mov,ogg|max:20000',
        ]);

        if ($request->hasFile('video')) {
            $file = $request->file('video');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('assets/media'), $filename);

            Setting::updateOrCreate(
                ['jenis_set' => 'Video'],
                ['value' => $filename]
            );

            return response()->json(['message' => 'Video berhasil diperbarui', 'filename' => $filename]);
        }

        return response()->json(['message' => 'Gagal upload video'], 400);
    }

    /**
     * Export History to CSV (Excel Compatible)
     */
    public function exportHistory(Request $request)
    {
        $type = $request->query('type', 'CS');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $today = Carbon::now()->format('Y-m-d');

        // Allow date range or default to today
        $query = Queue::where('type', $type);

        if ($startDate && $endDate) {
            $query->whereDate('tgl_antri', '>=', $startDate)
                  ->whereDate('tgl_antri', '<=', $endDate);
        } else {
             // If no date provided, default to today
             $query->whereDate('tgl_antri', $today);
        }

        // Only Finished or Skipped
        $query->whereIn('st_antrian', ['3', '4']);
        
        $queues = $query->orderBy('tgl_antri', 'desc')
                        ->orderBy('id_antrian', 'desc')
                        ->get();

        $filename = "Export_Antrian_{$type}_" . date('Ymd_His') . ".csv";
        
        $headers = array(
            "Content-type"        => "text/csv; charset=utf-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        );

        $callback = function() use($queues, $type) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for Excel UTF-8 compatibility
            fputs($file, "\xEF\xBB\xBF");

            // CSV Headers
            if ($type === 'CS') {
                fputcsv($file, [
                    'No Antrian', 
                    'Tanggal', 
                    'Waktu Mulai', 
                    'Waktu Selesai', 
                    'Nama Nasabah', 
                    'No Kontak', 
                    'Keperluan / Masalah', 
                    'Solusi / Tindakan', 
                    'Status',
                    'Durasi (Menit)'
                ], ";"); // Semicolon for Excel Indonesia/European format
            } else {
                // Teller Headers (Detailed)
                fputcsv($file, [
                    'No Antrian', 
                    'Tanggal', 
                    'Jam Panggil', 
                    'Jam Selesai', 
                    'Durasi (Menit)',
                    'Status',
                    'Jenis Transaksi', 
                    'Nominal (IDR)',
                    
                    // Data Nasabah / Pelaku
                    'Nama Nasabah', 
                    'No. Rekening',
                    'No. HP',
                    'Alamat',
                    'No. Identitas', // KTP/SIM

                    // Data Penerima (Khusus Transfer)
                    'Nama Penerima',
                    'Bank Tujuan',
                    'No. Rekening Tujuan',
                    'Negara Tujuan',
                    
                    // Detail Transaksi
                    'Berita / Keterangan',
                    'Metode Transfer',
                    'Sumber Dana',
                    'Biaya Admin'
                ], ";");
            }

            foreach ($queues as $q) {
                // Determine Service Duration (Call Time to Finish Time)
                // Fallback to Created Time if Call Time is missing
                $start = $q->waktu_panggil ? Carbon::parse($q->waktu_panggil) : Carbon::parse($q->created_at);
                $end = Carbon::parse($q->updated_at);
                
                // Calculate duration in minutes, ensure positive, and round to 0 decimals
                $diff = $start->floatDiffInMinutes($end);
                $duration = round(abs($diff));
                
                $status = ($q->st_antrian == 3) ? 'Selesai' : 'Dilewati';

                if ($type === 'CS') {
                    fputcsv($file, [
                        $q->antrian,
                        $q->tgl_antri,
                        $start->format('H:i:s'),
                        $end->format('H:i:s'),
                        $q->nama ?? '-',
                        $q->no_kontak != '0' ? $q->no_kontak : '-',
                        $q->tujuan_datang ?? '-',
                        $q->solusi ?? '-',
                        $status,
                        $duration . ' Menit'
                    ], ";");
                } else {
                    // Teller Data Extraction
                    // Initialize default 'clean' variables
                    $tx_type = $q->tx_type;
                    $nominal = 0;
                    
                    // Customer Data
                    $customer_nama = '-';
                    $customer_rekening = '-';
                    $customer_hp = '-';
                    $customer_alamat = '-';
                    $customer_id = '-';

                    // Recipient Data
                    $recipient_nama = '-';
                    $recipient_bank = '-';
                    $recipient_rekening = '-';
                    $recipient_country = '-';

                    // Details
                    $berita = '-';
                    $metode = '-';
                    $sumber = '-';
                    $biaya = 0;

                    // Extract based on Transaction Type
                    if ($q->kode) {
                        if (str_starts_with($q->kode, 'ST-')) {
                            // SETOR TUNAI
                            $tx = \App\Models\Transaction::where('token', $q->kode)->first();
                            if ($tx) {
                                $nominal = $tx->nominal;
                                $customer_nama = $tx->nama_penyetor;
                                $customer_rekening = $tx->no_rek; 
                                $customer_hp = $tx->hp_penyetor;
                                $customer_alamat = $tx->alamat_penyetor;
                                $customer_id = $tx->noid_penyetor;
                                $berita = $tx->berita;
                                $sumber = 'Tunai';
                            }
                        } elseif (str_starts_with($q->kode, 'TT-')) {
                            // TARIK TUNAI
                            $tx = \App\Models\Withdrawal::where('token', $q->kode)->first();
                            if ($tx) {
                                $nominal = $tx->nominal;
                                $customer_nama = $tx->nama_penarik;
                                $customer_rekening = $tx->no_rek;
                                $customer_hp = $tx->hp_penarik;
                                $customer_alamat = $tx->alamat_penarik;
                                $customer_id = $tx->noid_penarik;
                                $berita = $tx->tujuan; // Use Tujuan as Berita
                                $sumber = 'Tabungan';
                            }
                        } elseif (str_starts_with($q->kode, 'ON-')) {
                            // TRANSFER
                            $tx = \App\Models\Transfer::where('token', $q->kode)->first();
                            if ($tx) {
                                $nominal = $tx->nominal;
                                // Pengirim
                                $customer_nama = $tx->nama; 
                                $customer_rekening = $tx->no_rek;
                                $customer_hp = $tx->hp_penyetor; 
                                $customer_alamat = $tx->alamat_;
                                
                                // Penerima
                                $recipient_nama = $tx->nama_tujuan;
                                $recipient_bank = $tx->bank_tujuan;
                                $recipient_rekening = $tx->no_rek_tujuan;
                                $recipient_country = $tx->negara_tujuan ?? 'Indonesia';

                                $berita = $tx->berita_tujuan;
                                $metode = $tx->metode_transfer;
                                $sumber = $tx->sumber_dana;
                                $biaya = $tx->biaya_trf;
                            }
                        }
                    }

                    // Helper to force string in Excel
                    $forceString = function($val) {
                        return ($val && $val != '-') ? "=\"$val\"" : $val;
                    };

                    fputcsv($file, [
                        $q->antrian,
                        $q->tgl_antri,
                        $start->format('H:i:s'),
                        $end->format('H:i:s'),
                        $duration, 
                        $status,
                        $tx_type,
                        $nominal, 
                        
                        $customer_nama,
                        $forceString($customer_rekening),
                        $forceString($customer_hp),
                        $customer_alamat,
                        $forceString($customer_id),

                        $recipient_nama,
                        $recipient_bank,
                        $forceString($recipient_rekening),
                        $recipient_country,

                        $berita,
                        $metode,
                        $sumber,
                        $biaya
                    ], ";");
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
