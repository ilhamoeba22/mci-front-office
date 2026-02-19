<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Survey;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class SurveyController extends Controller
{
    /**
     * Tampilan layar survey untuk nasabah (Tablet Mode)
     */
    public function index()
    {
        return view('survey.index');
    }

    /**
     * Menyimpan hasil survey ke database
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'rating' => 'required|integer|between:1,5',
                'user_id' => 'required|exists:users,id',
                'reference_no' => 'required|string',
            ]);

            // Check if this reference (queue token) has already submitted a survey
            $exists = Survey::where('reference_no', $request->reference_no)->exists();
            if ($exists) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Survey untuk antrian ini sudah diisi.'
                ], 422);
            }

            // Tentukan user_id (Staff yang dinilai)
            $targetUserId = $request->user_id ?: $request->staff_id;

            // Debug log untuk memastikan data masuk
            Log::info("Survey Submission recorded:", [
                'user_id' => $targetUserId,
                'rating' => $request->rating,
                'ref' => $request->reference_no
            ]);

            // Reset status aktif setelah diisi agar layar tablet kembali standby
            if ($targetUserId) {
                Cache::forget("survey_active_{$targetUserId}");
            }

            Survey::create([
                'user_id' => $targetUserId,
                'rating' => $request->rating,
                'reference_no' => $request->reference_no,
                'comment' => null // Dimatikan sesuai permintaan
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Terima kasih atas penilaian Anda!'
            ]);
        } catch (\Exception $e) {
            Log::error('Survey Store Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan survey'
            ], 500);
        }
    }

    /**
     * Mengaktifkan layar survey untuk staff tertentu
     */
    public function trigger(Request $request)
    {
        $staffId = $request->staff_id;
        if (!$staffId) return response()->json(['error' => 'No staff ID'], 400);

        Cache::put("survey_active_{$staffId}", true, 60); // Aktif selama 60 detik
        return response()->json(['status' => 'success']);
    }

    /**
     * Cek apakah ada survey aktif untuk layar nasabah (Polling)
     */
    public function checkStatus(Request $request)
    {
        $staffId = $request->staff_id;
        $role = $request->role;
        $counter = $request->counter;

        $user = null;
        if ($staffId) {
            $user = \App\Models\User::find($staffId);
        } elseif ($role && $counter) {
            $user = \App\Models\User::where('role', strtolower($role))
                        ->where('counter_no', $counter)
                        ->first();
        }

        if (!$user) {
            return response()->json(['is_active' => false]);
        }

        $staffId = $user->id;

        // 1. Cek antrian yang sedang aktif dipanggil hari ini
        $activeQueue = \App\Models\Queue::where('type', strtoupper($user->role))
            ->whereDate('tgl_antri', \Carbon\Carbon::today())
            ->where('st_antrian', '2') // Status: Dipanggil
            ->orderBy('id_antrian', 'desc')
            ->first();

        $isActive = false;
        $referenceNo = null;

        if ($activeQueue) {
            // Cek apakah sudah pernah mengisi survey untuk nomor antrian ini
            $alreadyVoted = Survey::where('reference_no', $activeQueue->kode)->exists();
            if (!$alreadyVoted) {
                $isActive = true;
                $referenceNo = $activeQueue->kode;
            }
        }

        // 2. Fallback ke Manual Trigger (Jika tombol "Tampilkan Survey" diklik)
        if (!$isActive) {
            $isActive = \Illuminate\Support\Facades\Cache::get("survey_active_{$staffId}", false);
            if ($isActive && $activeQueue) {
                $referenceNo = $activeQueue->kode;
            }
        }

        return response()->json([
            'is_active' => $isActive,
            'staff' => [
                'id' => $user->id,
                'name' => $user->name,
                'role' => strtoupper($user->role),
                'counter_no' => $user->counter_no
            ],
            'reference_no' => $referenceNo
        ]);
    }
}
