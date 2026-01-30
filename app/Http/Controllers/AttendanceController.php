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
            } catch (\Exception $e) {}
        }

        $attendances = $attendanceQuery->paginate(10)->withQueryString();
        $users = User::orderBy('name')->get();

        return Inertia::render('Attendance/Index', compact('attendances', 'users'));
    }

    /**
     * API Store: Khusus Face Recognition (Menerima Tipe Check In/Out Explicit)
     */
    public function apiStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'required|in:check_in,check_out',
        ]);

        $name = $request->name;
        $type = $request->type;

        $user = User::where('name', $name)->first();

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'name' => $name,
                'message' => "User '{$name}' tidak ditemukan!",
            ], 404);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('check_in_time', $today)
                                ->first();

        // Logika Explicit dari Tombol (Check In / Check Out)
        if ($type === 'check_in') {
            if ($attendance) {
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda sudah Check-In hari ini!"
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

        } else { // Check Out
            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda belum Check-In hari ini!"
                ]);
            }

            if ($attendance->check_out_time) {
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda sudah Check-Out hari ini!"
                ]);
            }

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
     * Store Lama: Update menjadi Otomatis (Auto Detect) tanpa batasan jam.
     * Logika: Kalau belum masuk -> Check In. Kalau sudah masuk -> Check Out.
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
        $today = Carbon::today();

        // Cek status absen hari ini
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', $today)
            ->first();

        // KASUS 1: Belum ada data absen -> Lakukan CHECK IN
        if (!$attendance) {
            return $this->performCheckIn($user, $now);
        }

        // KASUS 2: Sudah Check In tapi belum Check Out -> Lakukan CHECK OUT
        if (is_null($attendance->check_out_time)) {
            return $this->performCheckOut($user, $now);
        }

        // KASUS 3: Sudah Check In & Sudah Check Out -> Tolak
        return response()->json([
            'success' => false,
            'message' => "Anda sudah selesai bekerja hari ini (Sudah Check-Out)."
        ]);
    }

    private function performCheckIn($user, $now)
    {
        Attendance::create([
            'user_id' => $user->id,
            'check_in_time' => $now,
            'status' => 'present' // Pastikan status terisi
        ]);

        return response()->json([
            'success' => true,
            'message' => "Selamat Pagi {$user->name}, Check-In Berhasil"
        ]);
    }

    private function performCheckOut($user, $now)
    {
        // Cari absen hari ini yang belum check out
        $attendance = Attendance::where('user_id', $user->id)
            ->whereDate('check_in_time', Carbon::today())
            ->whereNull('check_out_time')
            ->first();

        if ($attendance) {
            $attendance->update([
                'check_out_time' => $now,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => "Selamat Sore {$user->name}, Anda Berhasil Check-Out!",
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => "Gagal memproses Check-Out."
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

        return Excel::download(new AttendanceMultiSheetExport($query), $filename);
    }
}