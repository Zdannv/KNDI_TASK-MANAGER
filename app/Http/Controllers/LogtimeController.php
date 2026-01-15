<?php

namespace App\Http\Controllers;

use App\Models\Logtime;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Carbon\Carbon;
use App\Exports\LogtimeMultiSheetExport;
use App\Exports\SummaryLogtimeExport;
use Maatwebsite\Excel\Facades\Excel;

class LogtimeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();

        $logtimesQuery = Logtime::with('task')->orderBy('date', 'desc');
        if (isset($query['user_id']) && in_array(Auth::user()->role, ['other', 'co'])) {
            $logtimesQuery->where('user_id', $query['user_id']);
        } else {
            $logtimesQuery->where('user_id', Auth::id());
        }

        if (isset($query['from']) && isset($query['to'])) {
            $from = Carbon::parse($query['from']);
            $to = Carbon::parse($query['to']);
            $logtimesQuery->whereBetween('date', [$from, $to]);
        }
        $logtimes = $logtimesQuery->get();

        $tasksQuery = Task::query()->with('project');
        if (Auth::user()->role == 'pg') {
            $tasksQuery->where(function ($query) {
                $query->whereJsonContains('programmer', Auth::id())
                    ->orWhereJsonContains('reviewer', Auth::id());
            });
        } elseif (Auth::user()->role == 'ds') {
            $tasksQuery->whereJsonContains('designer', Auth::id());
        }
        $tasks = $tasksQuery->get();

        $users = User::get();

        return Inertia::render('User/Logtime', compact('logtimes', 'tasks', 'users'));   
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'date' => 'required|string',
            'task_id' => 'required',
            'time_used' => 'required|numeric'
        ]);

        $timeUsed = $request->time_used < 0 ? 1 : $request->time_used;
        $task = Task::where('id', $request->task_id)->first();
        $logtime = $task->logtimes()->create([
            'user_id' => Auth::id(),
            'date' => Carbon::parse($request->date),
            'time_used' => $timeUsed
        ]);

        Auth::user()->logs()->create([
            'target' => 'logtime',
            'description' => "[CREATE] logtime {$logtime->task->ticket_link} for {$logtime->time_used} hours on {$logtime->date}",
        ]);

        return back()->with('success', "Logtime '{$logtime->task->ticket_link}' sebanyak {$logtime->time_used} jam pada {$logtime->date} berhasil ditambahkan!");
    }

    /**
     * Delete logtime.
     */
    public function destroy($id): RedirectResponse
    {
        $logtime = Logtime::with('user')->where('id', $id)->first();
        $logtime->delete();

        Auth::user()->logs()->create([
            'target' => 'logtime',
            'description' => "[DELETE]  logtime {$logtime->task->ticket_link} for {$logtime->time_used} hours on {$logtime->date}",
        ]);

        return back()->with('success', "Logtime '{$logtime->task->ticket_link}' sebanyak {$logtime->time_used} jam pada {$logtime->date} berhasil dihapus!");
    }

    /**
     * Export logtime.
     */ 
    public function export(Request $request)
    {
        $query = $request->query();

        $isSummary = isset($query['summary']) && filter_var($query['summary'], FILTER_VALIDATE_BOOLEAN);

        $filename = $isSummary ? 'summary_logtimes.xlsx' : 'logtimes.xlsx';
        
        if (isset($query['user_id'])) {
            $user = User::where('id', $query['user_id'])->first();
            if ($user) {
                $prefix = $user->name;
                $filename = $isSummary 
                    ? "{$prefix}_summary_logtime.xlsx" 
                    : "{$prefix}_logtime.xlsx";
            }
        }

        Auth::user()->logs()->create([
            'target' => 'logtime',
            'description' => "[EXPORT] " . ($isSummary ? 'summary logtime' : 'logtime'),
        ]);

        return Excel::download(new LogtimeMultiSheetExport($query), $filename);
    }
}
