<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Carbon\Carbon;

use App\Models\ProjectOwner;
use App\Models\Task;
use App\Models\User;
use App\Models\Project;
use App\Models\PullRequest;
use App\Models\TaskReviewer;

use App\Mail\TaskAssignmentNotification;
use App\Mail\TaskUnassignmentNotification;
use App\Mail\TaskCommentNotification;
use App\Mail\CommentReplyNotification;
use App\Mail\TaskReviewCompletedNotification;

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

        $tasks = $tasksQuery->orderByRaw('ISNULL(due_date), due_date ASC')
            ->paginate(10)
            ->withQueryString();
        $projects = Project::with('projectOwner')->where('isDeleted', false)->get();
        
        $users = User::get();
        $communicator = User::where('role', 'co')->get();
        $programmer = User::where('role', 'pg')->orWhere('role', 'pm')->get();
        $designer = User::where('role', 'ds')->get();

        return Inertia::render('Task/Index', compact('tasks', 'projects', 'users', 'communicator', 'programmer', 'designer'));   
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'project_id' => 'required',
            'issue' => 'required|string|max:255',
            'ticket_link' => 'required|string|max:255',
            'related_links' => 'array',
            'description' => 'nullable|string',
            'start_date' => 'required|string',
            'due_date' => 'string',
        ]);

        $project = Project::where('id', $request->project_id)->firstOrFail();

        $task = $project->tasks()->create([
            'issue' => $request->issue,
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
     * Update the task's assignments (pl, co, pg, ds) and send notifications.
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

        // 1. SNAPSHOT DATA LAMA (Untuk cek siapa yang dihapus)
        $oldAssignments = [
            'Programmer'   => $task->programmer ?? [],
            'Designer'     => $task->designer ?? [],
            'Communicator' => $task->communicator ?? [],
        ];
        
        // 2. UPDATE DATABASE
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

        // 3. LOGIKA NOTIFIKASI LENGKAP (ASSIGN & UNASSIGN)
        $newAssignments = [
            'Programmer'   => $request->programmer ?? [],
            'Designer'     => $request->designer ?? [],
            'Communicator' => $request->communicator ?? [],
        ];

        foreach ($newAssignments as $role => $newIds) {
            $oldIds = $oldAssignments[$role];

            // A. Kirim ke yang BARU DITAMBAH (Assign)
            $addedIds = array_diff($newIds, $oldIds);
            if (!empty($addedIds)) {
                $users = User::whereIn('id', $addedIds)->get();
                foreach ($users as $user) {
                    if ($user->email) {
                        try {
                            Mail::to($user->email)->send(new TaskAssignmentNotification($task, $role));
                        } catch (\Exception $e) {
                            Log::error("Gagal kirim email assign ke {$user->email}: " . $e->getMessage());
                        }
                    }
                }
            }

            // B. Kirim ke yang DIHAPUS (Unassign)
            $removedIds = array_diff($oldIds, $newIds);
            if (!empty($removedIds)) {
                $users = User::whereIn('id', $removedIds)->get();
                foreach ($users as $user) {
                    if ($user->email) {
                        try {
                            // Pastikan Class Mail 'TaskUnassignmentNotification' sudah dibuat
                            Mail::to($user->email)->send(new TaskUnassignmentNotification($task, $role));
                        } catch (\Exception $e) {
                            Log::error("Gagal kirim email unassign ke {$user->email}: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        return back()->with('success', "Task berhasil di-assign dan notifikasi terkirim!");
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

        $newReviewerIds = !empty($request->reviewer) ? array_values(array_filter($request->reviewer)) : [];
        $existingReviewerIds = $task->reviewers()->pluck('user_id')->toArray();

        // Cari Perbedaan (Siapa yang ditambah, siapa yang dibuang)
        $toAdd = array_values(array_diff($newReviewerIds, $existingReviewerIds));
        $toRemove = array_values(array_diff($existingReviewerIds, $newReviewerIds));

        // 1. Handle Penambahan Reviewer (ASSIGN)
        foreach ($toAdd as $uid) {
            TaskReviewer::updateOrCreate(
                ['task_id' => $task->id, 'user_id' => $uid],
                ['status' => 'pending']
            );

            $user = User::find($uid);
            if ($user && $user->email) {
                try {
                    // Gunakan Class Mail Assign yang sudah terbukti berhasil
                    Mail::to($user->email)->send(new TaskAssignmentNotification($task, 'Reviewer'));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim email review ke {$user->email}");
                }
            }
        }

        // 2. Handle Penghapusan Reviewer (UNASSIGN)
        foreach ($toRemove as $uid) {
            $tr = TaskReviewer::where('task_id', $task->id)->where('user_id', $uid)->first();
            if ($tr) {
                $tr->status = 'removed';
                $tr->save();
            }

            $user = User::find($uid);
            if ($user && $user->email) {
                try {
                    // Gunakan Class Mail Unassign yang sudah kita buat tadi
                    Mail::to($user->email)->send(new TaskUnassignmentNotification($task, 'Reviewer'));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal kirim email unassign reviewer ke {$user->email}");
                }
            }
        }

        // Update Kolom JSON di Tabel Task (untuk backup/display)
        $task->update([
            'reviewer' => !empty($newReviewerIds) ? $newReviewerIds : null,
            'isAssign' => true,
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[ASSIGN PR] task for {$task->issue}",
        ]);

        return back()->with('success', "Reviewer berhasil diupdate dan notifikasi dikirim!");
    }
    
    /**
     * Add comment for task (General Notification).
     */
    public function commentTask(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'pr_links' => 'nullable|array',
            'comment' => 'required|string'
        ]);
        
        $task = Task::findOrFail($id);
        
        // Simpan Comment
        $pullRequest = $task->pullRequests()->create([
            'pr_links' => !empty($request->pr_links) ? $request->pr_links : null,
            'comment' => $request->comment,
            'from' => Auth::id(),
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[COMMENT] task for {$task->issue}",
        ]);

        // --- NOTIFIKASI KOMENTAR ---
        $recipientIds = collect([]);

        if ($task->creator) $recipientIds->push($task->creator);
        if ($task->pl) $recipientIds->push($task->pl);
        if (!empty($task->programmer)) $recipientIds = $recipientIds->merge($task->programmer);
        if (!empty($task->designer)) $recipientIds = $recipientIds->merge($task->designer);
        if (!empty($task->communicator)) $recipientIds = $recipientIds->merge($task->communicator);
        if (!empty($task->reviewer)) $recipientIds = $recipientIds->merge($task->reviewer);

        // Hapus duplikat & hapus diri sendiri
        $uniqueRecipients = $recipientIds->unique()->reject(fn($id) => $id == Auth::id());

        if ($uniqueRecipients->isNotEmpty()) {
            $users = User::whereIn('id', $uniqueRecipients)->get();
            foreach ($users as $user) {
                if ($user->email) {
                    try {
                        // UPDATE: Kita kirim object $pullRequest (bukan string comment biasa)
                        Mail::to($user->email)->send(new TaskCommentNotification($task, Auth::user(), $pullRequest));
                    } catch (\Exception $e) {
                        Log::error("Gagal kirim notif komentar ke {$user->email}: " . $e->getMessage());
                    }
                }
            }
        }

        return back()->with('success', "Komentar berhasil ditambahkan!");
    }

    /**
     * Reply to a comment (Specific Notification).
     */
    public function replyTask(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'pr_links' => 'nullable|array',
            'comment' => 'required|string'
        ]);
        
        $parentComment = PullRequest::with('task')->where('id', $id)->firstOrFail();

        // Cek Duplikat
        $exists = $parentComment->replies()
            ->where('from', Auth::id())
            ->where('comment', $request->comment)
            ->where('created_at', '>=', Carbon::now()->subSeconds(10))
            ->exists();

        if ($exists) {
            return back()->with('success', "Reply berhasil (terdeteksi duplikat, diabaikan)");
        }

        // Simpan Reply
        $reply = $parentComment->replies()->create([
            'pr_links' => !empty($request->pr_links) ? $request->pr_links : null,
            'comment' => $request->comment,
            'from' => Auth::id(),
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[REPLY] task for {$parentComment->task->issue}",
        ]);

        // --- NOTIFIKASI REPLY ---
        $targetUserId = $parentComment->from;

        // Cek: Jangan kirim kalau balas komentar sendiri
        if ($targetUserId != Auth::id()) {
            $targetUser = User::find($targetUserId);
            
            if ($targetUser && $targetUser->email) {
                try {
                    // UPDATE: Kita kirim object $reply
                    Mail::to($targetUser->email)->send(new CommentReplyNotification(
                        $parentComment->task, 
                        Auth::user(),         
                        $reply // Kirim object reply lengkap
                    ));
                } catch (\Exception $e) {
                    Log::error("Gagal kirim notif reply ke {$targetUser->email}: " . $e->getMessage());
                }
            }
        }

        return back()->with('success', "Reply berhasil dikirim!");
    }
    /**
     * Display a detail of the resource.
     */
    public function show($id) 
    {
        $task = Task::with(['logtimes','reviewers.user','pullRequests.replies.user', 'pullRequests.user'])->findOrFail($id);

        $users = User::get();
        $project = Project::where('id', $task->project_id)->with('projectOwner')->first();
        $projects = Project::with('projectOwner')->get();

        $communicator = User::where('role', 'co')->get();
        $programmer = User::where('role', 'pg')->orWhere('role', 'pm')->get();
        $designer = User::where('role', 'ds')->get();

        $totalTimeUsed = $task->logtimes->sum('time_used');

        $prs = PullRequest::with(['replies.user', 'user'])->where('task_id', $id)->get();

        return Inertia::render('Task/Show', compact('task', 'users', 'project', 'projects', 'communicator', 'programmer', 'designer', 'totalTimeUsed', 'prs'));   
    }

    /**
     * Change the task's isActive.
     */
    public function changeIsActive($id): RedirectResponse
    {
        $task = Task::findOrFail($id);
        $task->update([
            'isActive' => ! $task->isActive,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'task',
            'description' => "[CHANGE ISACTIVE] task for {$task->issue}",
        ]);

        return back()->with('success', "Status active berhasil diperbarui!");
    }

    /**
     * Delete the task.
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

    /**
     * Mark review as complete by reviewer
     */
    public function markReviewComplete(Request $request, $taskId, $reviewerId)
    {
        if (Auth::id() != $reviewerId) {
            abort(403);
        }

        $tr = TaskReviewer::where('task_id', $taskId)->where('user_id', $reviewerId)->firstOrFail();
        $tr->status = 'done';
        $tr->completed_at = Carbon::now();
        $tr->save();

        $task = Task::findOrFail($taskId);
        $recipientIds = [];
        if ($task->pl) $recipientIds[] = $task->pl;
        if ($task->creator) $recipientIds[] = $task->creator;
        $recipientIds = array_values(array_unique(array_filter($recipientIds)));

        if (count($recipientIds) > 0) {
            $users = User::whereIn('id', $recipientIds)->get();
            foreach ($users as $user) {
                if ($user->email) {
                    try {
                        Mail::to($user->email)->send(new TaskReviewCompletedNotification($task, $tr));
                    } catch (\Exception $e) {
                        Log::error("Gagal kirim task review completed ke {$user->email}: " . $e->getMessage());
                    }
                }
            }
        }

        return response()->json(['message' => 'Marked as reviewed']);
    }
}