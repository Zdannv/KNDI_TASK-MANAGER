<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;
// use App\Exports\AttendanceExport; // Jika nanti ingin implementasi export real
// use Maatwebsite\Excel\Facades\Excel;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->query();
        
        // Eager load user relationship
        $attendanceQuery = Attendance::with('user')->orderBy('check_in_time', 'desc');

        // Filter by User
        if (isset($query['user_id'])) {
            $attendanceQuery->where('user_id', $query['user_id']);
        }

        // Filter by Date Range
        if (isset($query['from']) && isset($query['to'])) {
            $from = Carbon::parse($query['from'])->startOfDay();
            $to = Carbon::parse($query['to'])->endOfDay();
            $attendanceQuery->whereBetween('check_in_time', [$from, $to]);
        }

        $attendances = $attendanceQuery->get();
        $users = User::orderBy('name')->get();

        return Inertia::render('Attendance/List', compact('attendances', 'users'));
    }

    public function export(Request $request)
    {
        // Logika export bisa disamakan dengan LogtimeExport
        // return Excel::download(new AttendanceExport($request->query()), 'attendance.xlsx');
        return back()->with('success', 'Fitur export sedang disiapkan.');
    }
}
