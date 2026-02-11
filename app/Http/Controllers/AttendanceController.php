<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
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
            } catch (\Exception $e) {
                // Jika format tanggal salah, filter diabaikan
            }
        }

        $attendances = $attendanceQuery->paginate(10)->withQueryString();
        $users = User::orderBy('name')->get();

        return Inertia::render('Attendance/Index', compact('attendances', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|string',
            'type' => 'required|in:check_in,check_out',
        ]);

        $verification = $this->verifyFace($request->image);

        if (!$verification || !isset($verification['success'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Proses verifikasi gagal dijalankan'
            ], 500);
        }

        if (!$verification['success']) {
            return response()->json([
                'status' => 'error',
                'message' => $verification['message'],
            ], 401);
        }

        $user = User::find($verification['user_id']);

        if (!$user) {
            return response()->json([
                'status' => 'error',
                'message' => 'User dengan id ' . $verification['user_id'] . ' tidak ditemukan di database',
            ], 404);
        }

        $today = Carbon::today();
        $attendance = Attendance::where('user_id', $user->id)
                                ->whereDate('check_in_time', $today)
                                ->first();

        $confidence =  $verification['confidence'];

        if ($request->type === 'check_in') {
            if ($attendance) {
                $checkIn = Carbon::parse($attendance->check_in_time)->format('H:i');
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda Sudah Melakukan Check-In"
                ]);
            }

            Attendance::create([
                'user_id' => $user->id,
                'check_in_time' => Carbon::now(),
                'check_in_confidence' => $confidence
            ]);

            return response()->json([
                'status' => 'success',
                'name' => $user->name,
                'message' => "Berhasil Check-In"
            ]);
        } else {
            if (!$attendance) {
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda Belum Melakukan Check-In!",
                ]);
            }

            if ($attendance->check_out_time) {
                return response()->json([
                    'status' => 'error',
                    'name' => $user->name,
                    'message' => "Anda Sudah Melakukan Check-Out",
                ]);
            }

            $attendance->update([
                'check_out_time' => Carbon::now(),
                'check_out_confidence' => $confidence,
            ]);

            return response()->json([
                'status' => 'success',
                'name' => $user->name,
                'message' => "Berhasil Check-Out",
            ]);
        }
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

    private function verifyFace($imageBase64)
    {
        $host = env('PYTHON_SERVICE');
        $port = env('PYTHON_SERVICE_PORT');

        $users = User::whereNotNull('face_embedding')->get(['id', 'name', 'face_embedding']);

        $userData = $users->map(function ($u) {
            return [
                'id' => $u->id,
                'name' => $u->name,
                'embedding' => $u->face_embedding,
            ];
        });
        
        try {
            $imageRaw = $imageBase64;
            if (str_contains($imageRaw, ',')) {
                $imageRaw = explode(',', $imageRaw)[1];
            }

            $response = Http::timeout(5)->post("http://{$host}:{$port}/attendance", [
                'image' => $imageBase64,
                'users' => $userData, 
            ]);

            $result = $response->json();

            if ($response->successful() && isset($result['match'])) {
                return [
                    'success' => $result['match'],
                    'user_id' => $result['message'] ?? null,
                    'confidence' => $result['confidence'] ?? 0
                ];
            }

            return [
                'success' => false, 
                'message' => $result['message'] ?? 'Wajah tidak dikenali atau error dari server Python',
                'confidence' => 0
            ];
        } catch (\Exception $err) {
            return [
                'success' => false, 
                'message' => 'Gagal koneksi ke server: ' . $err->getMessage(),
                'confidence' => 0
            ];
        }
    }
}