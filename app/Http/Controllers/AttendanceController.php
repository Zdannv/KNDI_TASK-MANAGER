<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Exports\AttendanceMultiSheetExport;
use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    /**
     * Menampilkan halaman log absensi (Dashboard Admin)
     */
    public function index(Request $request)
    {
        $query = $request->query();
        
        $attendanceQuery = Attendance::with('user')->orderBy('check_in_time', 'desc');

        if (isset($query['user_id'])) {
            $attendanceQuery->where('user_id', $query['user_id']);
        }

        if (!empty($query['from']) && !empty($query['to'])) {
            try {
                $from = Carbon::createFromFormat('d-m-Y', $query['from'])->startOfDay();
                $to = Carbon::createFromFormat('d-m-Y', $query['to'])->endOfDay();
                
                $attendanceQuery->whereBetween('check_in_time', [$from, $to]);
            } catch (\Exception $e) {
                // Jika format tanggal salah, filter diabaikan
            }
        }

        $attendances = $attendanceQuery->paginate(10)->withQueryString();
        $users = User::orderBy('name')->get();

        return Inertia::render('Attendance/Index', compact('attendances', 'users'));
    }

    /**
     * [BARU] API Store khusus untuk Face Recognition Kiosk
     * Menerima 'name' dari Python dan 'type' (check_in/out) dari Vue.
     * Tidak ada batasan jam (bebas jam berapa saja).
     */
    public function apiStore(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:check_in,check_out',
        ]);

        $name = $request->name;
        $type = $request->type;

        // 2. Cari User Berdasarkan Nama
        $user = User::where('name', $name)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'name' => $name,
                'message' => "User '{$name}' tidak ditemukan di database!",
            ], 404);
        }

        // 3. Cek Data Absen Hari Ini
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('check_in_time', $today)
                                ->first();

        // 4. Logika Check In / Check Out
        if ($type === 'check_in') {
            // --- KASUS CHECK IN ---
            if ($attendance) {
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda sudah melakukan Check-In hari ini!"
                ]);
            }

            Attendance::create([
                'user_id' => $user->id,
                'status' => 'present',
                'check_in_time' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 'success',
                'name' => $user->name,
                'message' => "Berhasil Check-In"
            ]);

        } else {
            // --- KASUS CHECK OUT ---
            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda belum melakukan Check-In hari ini!"
                ]);
            }

            // Update waktu pulang (overwrite jika scan berkali-kali)
            $attendance->update([
                'check_out_time' => Carbon::now(),
            ]);

            return response()->json([
                'status' => 'success',
                'name' => $user->name,
                'message' => "Berhasil Check-Out"
            ]);
        }
    }

    /**
     * [LAMA] Store manual (biasanya dipakai untuk testing / input manual)
     * Memiliki batasan jam kerja.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
        ]);

        $user = User::where('name', $request->name)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => "User Tidak Ditemukan!",
            ], 404);
        }

        $now = Carbon::now();
        $currentTime = $now->format('H:i');

        // Logika Jam Kerja (Hardcoded)
        if ($currentTime >= '07:00' && $currentTime <= '12:00') {
            return $this->performCheckIn($user, $now);
        }

        if ($currentTime >= '16:00' && $currentTime <= '18:00') {
            return $this->performCheckOut($user, $now);
        }

        return response()->json([
            'success' => false,
            'message' => "Bukan Waktu Absensi"
        ], 403);
    }

    /**
     * Helper untuk method store lama
     */
    private function performCheckIn($user, $now)
    {
        $today = Carbon::today();
        $exists = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', $today)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => "Anda sudah melakukan absensi!"
            ]);
        }

        Attendance::create([
            'user_id' => $user->id,
            'check_in_time' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Selamat Pagi {$user->name}, Check-In Berhasil"
        ]);
    }

    /**
     * Helper untuk method store lama
     */
    private function performCheckOut($user, $now)
    {
        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', $today)
            ->whereNull('check_out_time')
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => "Data Check-In Tidak Ada atau Anda Sudah Melakukan Absensi!"
            ]);
        }

        $attendance->update([
            'check_out_time' => $now,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Selamat Sore {$user->name}, Anda Berhasil Check-Out!",
        ]);
    }

    public function export(Request $request)
    {
        $query = $request->query();

        $isSummary = isset($query['summary']) && filter_var($query['summary'], FILTER_VALIDATE_BOOLEAN);

        $filename = $isSummary ? 'summary_attendance.xlsx' : 'attendance_report.xlsx';
        
        if (isset($query['user_id'])) {
            $user = User::where('id', $query['user_id'])->first();
            if ($user) {
                $prefix = str_replace(' ', '_', $user->name);
                $filename = $isSummary 
                    ? "{$prefix}_summary_attendance.xlsx" 
                    : "{$prefix}_attendance.xlsx";
            }
        }

        // Jalankan download Excel
        return Excel::download(new AttendanceMultiSheetExport($query), $filename);
    }
}