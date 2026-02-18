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
                'rating' => 'required|integer|between:1,4',
                'user_id' => 'nullable|exists:users,id',
                'reference_no' => 'nullable|string',
                'comment' => 'nullable|string'
            ]);

            // Reset status aktif setelah diisi
            if ($request->staff_id) {
                Cache::forget("survey_active_{$request->staff_id}");
            }

            Survey::create([
                'user_id' => $request->user_id ?? 1,
                'rating' => $request->rating,
                'reference_no' => $request->reference_no,
                'comment' => $request->comment
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

        // Jika tablet menggunakan role & counter, cari staff_id yang sesuai
        if (!$staffId && $role && $counter) {
            $user = \App\Models\User::where('role', strtolower($role))
                        ->where('counter_no', $counter)
                        ->first();
            $staffId = $user ? $user->id : null;
        }

        $isActive = $staffId ? Cache::get("survey_active_{$staffId}", false) : false;

        $staff = null;
        if ($staffId) {
            $user = \App\Models\User::find($staffId);
            if ($user) {
                $staff = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'counter_no' => $user->counter_no,
                    'role' => strtoupper($user->role)
                ];
            }
        }

        return response()->json([
            'is_active' => $isActive,
            'staff' => $staff
        ]);
    }
}
