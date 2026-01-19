<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
// Tambahkan Import Ini
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

        if (isset($query['from']) && isset($query['to'])) {
            $from = Carbon::parse($query['from'])->startOfDay();
            $to = Carbon::parse($query['to'])->endOfDay();
            $attendanceQuery->whereBetween('check_in_time', [$from, $to]);
        }

        $attendances = $attendanceQuery->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Attendance/List', compact('attendances', 'users'));
    }

    // Update Method Export
    public function export(Request $request)
    {
        $query = $request->query();

        // Cek apakah user meminta summary atau detail
        $isSummary = isset($query['summary']) && filter_var($query['summary'], FILTER_VALIDATE_BOOLEAN);

        // Tentukan nama file
        $filename = $isSummary ? 'summary_attendance.xlsx' : 'attendance_report.xlsx';
        
        // Jika filter per user, tambahkan nama user di nama file
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