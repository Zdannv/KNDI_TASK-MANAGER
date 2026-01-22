<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Models\ProjectOwner;
use App\Models\Project;
use App\Models\PullRequest;
use App\Models\Logtime;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->query();

        $tasksQuery = Task::query()->with('project');

        if (isset($query['project_id'])) {
            $tasksQuery->where('project_id', $query['project_id']);
        }
        if (isset($query['search'])) {
            $tasksQuery->where('issue', 'LIKE', '%' . $query['search'] . '%')
                ->orWhere('ticket_link', 'LIKE', '%' . $query['search'] . '%');
        }

        if (in_array(Auth::user()->role, ['pm', 'pg', 'ds'])) {
            $tasksQuery->where('isActive', true);
        }

        if (Auth::user()->role == 'pg') {
            $tasksQuery->where(function ($query) {
                $query->whereJsonContains('programmer', Auth::id())
                    ->orWhereJsonContains('reviewer', Auth::id());
            });
        } elseif (Auth::user()->role == 'ds') {
            $tasksQuery->whereJsonContains('designer', Auth::id());
        }

        $tasks = $tasksQuery->orderByRaw('ISNULL(due_date), due_date ASC')->get();

        $projects = Project::where('isDeleted', false)->get();
        $users = User::get();
        $communicator = User::where('role', 'co')->get();
        $programmer = User::where('role', 'pg')->orWhere('role', 'pm')->get();
        $designer = User::where('role', 'ds')->get();

        return Inertia::render('Task/Index', compact('tasks', 'projects', 'users', 'communicator', 'programmer', 'designer'));   
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'project_id' => 'required',
            'issue' => 'required|string|max:255',
            'type' => 'required|in:low,normal,high',
            'ticket_link' => 'required|string|max:255',
            'related_links' => 'array',
            'description' => 'nullable|string',
            'start_date' => 'required|string',
            'due_date' => 'string',
        ]);

        $project = Project::where('id', $request->project_id)->firstOrFail();

        $task = $project->tasks()->create([
            'issue' => $request->issue,
            'type' => $request->type,
            'ticket_link' => $request->ticket_link,
            'related_links' => !empty($request->related_links) ? $request->related_links : null,
            'description' => $request->description,
            'start_date' => Carbon::parse($request->start_date),
            'due_date' => $request->due_date ? Carbon::parse($request->due_date) : null,
            'creator' => Auth::id(),
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[CREATE] task for {$task->issue}",
        ]);

        return back()->with('success', "Task '{$task->issue}' berhasil dibuat!");
    }

    /**
     * Update the task's data.
     */
    public function editData(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'project_id' => 'required',
            'issue' => 'required|string|max:255',
            'type' => 'required|in:low,normal,high',
            'ticket_link' => 'required|string|max:255',
            'related_links' => 'array',
            'description' => 'nullable|string',
            'start_date' => 'required|string',
            'due_date' => 'nullable|string',
        ]);

        $project = Project::where('id', $request->project_id)->firstOrFail();

        $task = Task::findOrFail($id);
        $task->update([
            'project_id' => $project->id,
            'issue' => $request->issue,
            'type' => $request->type,
            'ticket_link' => $request->ticket_link,
            'related_links' => !empty($request->related_links) ? $request->related_links : null,
            'description' => $request->description,
            'start_date' => Carbon::parse($request->start_date),
            'due_date' => $request->due_date ? Carbon::parse($request->due_date) : null,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[UPDATE] task for {$task->issue}",
        ]);

        return back()->with('success', "Task '{$task->issue}' berhasil diperbarui!");
    }

    /**
     * Update the task's pl, co, pg, ds.
     */
    public function assignTask(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'pl' => 'nullable|numeric',
            'communicator' => 'array',
            'programmer' => 'array',
            'designer' => 'array',
        ]);
        
        $task = Task::findOrFail($id);
        $task->update([
            'pl' => $request->pl,
            'communicator' => !empty($request->communicator) ? $request->communicator : null,
            'programmer' => !empty($request->programmer) ? $request->programmer : null,
            'designer' => !empty($request->designer) ? $request->designer : null,
            'isAssign' => true
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[ASSIGN] task for {$task->issue}",
        ]);

        return back()->with('success', "Task '{$task->issue}' berhasil di assign!");
    }

    /**
     * Update the task's reviewer, pr_links.
     */
    public function prTask(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'reviewer' => 'array',
        ]);
        
        $task = Task::findOrFail($id);
        $task->update([
            'reviewer' => !empty($request->reviewer) ? $request->reviewer : null,
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[ASSIGN PR] task for {$task->issue}",
        ]);

        return back()->with('success', "Task '{$task->issue}' reviewer berhasil di assign!");
    }

    /**
     * Add comment for task's pr .
     */
    public function commentTask(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'pr_links' => 'nullable|array',
            'comment' => 'required|string'
        ]);
        
        $task = Task::findOrFail($id);
        $task->pullRequests()->create([
            'pr_links' => !empty($request->pr_links) ? $request->pr_links : null,
            'comment' => $request->comment,
            'from' => Auth::id(),
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[COMMENT] task for {$task->issue}",
        ]);

        return back()->with('success', "Task '{$task->issue}' comment berhasil dibuat!");
    }

    /**
     * Reply comment for task's pr .
     */
    public function replyTask(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'pr_links' => 'nullable|array',
            'comment' => 'required|string'
        ]);
        
        $pr = PullRequest::with('task')->where('id', $id)->first();
        $pr->replies()->create([
            'pr_links' => !empty($request->pr_links) ? $request->pr_links : null,
            'comment' => $request->comment,
            'from' => Auth::id(),
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[REPLY COMMENT] task for {$pr->task->issue}",
        ]);

        return back()->with('success', "Task '{$pr->task->issue}' comment berhasil direply!");
    }

    /**
     * Display a detail of the resource.
     */
    public function show($id) 
    {
        // PERBAIKAN DI SINI: Tambahkan with('logtimes')
        $task = Task::with('logtimes')->findOrFail($id);

        $users = User::get();
        $project = Project::where('id', $task->project_id)->with('client')->first();
        $projects = Project::get();

        $communicator = User::where('role', 'co')->get();
        $programmer = User::where('role', 'pg')->orWhere('role', 'pm')->get();
        $designer = User::where('role', 'ds')->get();

        // PERBAIKAN: Hitung total langsung dari relasi (lebih efisien)
        $totalTimeUsed = $task->logtimes->sum('time_used');

        $prs = PullRequest::with('replies')->where('task_id', $id)->get();

        return Inertia::render('Task/Show', compact('task', 'users', 'project', 'projects', 'communicator', 'programmer', 'designer', 'totalTimeUsed', 'prs'));   
    }

    /**
     * Change the task's isActive.
     */
    public function changeIsActive($id): RedirectResponse
    {
        $task = Task::findOrFail($id);
        $updated = $task->update([
            'isActive' => ! $task->isActive,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[CHANGE ISACTIVE] task for {$task->issue}",
        ]);

        return back()->with('success', "Task '{$task->issue}' is active berhasil diperbarui!");
    }

    /**
     * Delete the task's data.
     */
    public function destroy($id): RedirectResponse
    {
        $task = Task::findOrFail($id);
        $task->delete();

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[DELETE] task for {$task->issue}",
        ]);

        return redirect(route('task.list', absolute: false))->with('warning', "Task '{$task->issue}' berhasil dihapus!");
    }

    /**
     * Close the task.
     */
    public function close($id): RedirectResponse
    {
        $task = Task::findOrFail($id);
        $task->update([
            'end_date' => Carbon::today(),
            'isActive' => false
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[CLOSE] task for {$task->issue}",
        ]);

        return redirect(route('task.list', absolute: false))->with('warning', "Task '{$task->issue}' berhasil diclose!");
    }
}