<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $projectOwnerId = $request->query('project_owner_id');
        $projectsQuery = Project::where('isDeleted', false)->with('projectOwner');
        if ($projectOwnerId) {
            $projectsQuery->where('project_owner_id', $projectOwnerId);
        }
        $projects = $projectsQuery->paginate(10)->withQueryString();
        $projectOwners = ProjectOwner::where('isDeleted', false)->get();
        $users = User::get();

        return Inertia::render('Project/Index', compact('projects', 'projectOwners', 'users'));   
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_owner_id' => 'required'
        ]);

        $projectOwner = ProjectOwner::where('id', $request->project_owner_id)->first();

        $project = $projectOwner->projects()->create([
            'name' => $request->name,
            'creator' => Auth::id(),
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project',
            'description' => "[CREATE] project {$project->name}",
        ]);

        return redirect(route('project.list', ['project_owner_id' => $request->query('project_owner_id')]))
            ->with('success', "Project '{$project->name}' berhasil dibuat!");
    }

    /**
     * Update the project's data.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'project_owner_id' => 'required'
        ]);

        $projectOwner = ProjectOwner::findOrFail($request->project_owner_id);

        $project = Project::findOrFail($id);
        $project->update([
            'name' => $request->name,
            'project_owner_id' => $projectOwner->id,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project',
            'description' => "[UPDATE] project {$project->name}",
        ]);

        return redirect(route('project.list', ['project_owner_id' => $request->query('project_owner_id')]))
            ->with('success', "Project '{$project->name}' berhasil diperbarui!");
    }

    /**
     * Delete the project's data.
     */
    public function destroy(Request $request, $id): RedirectResponse
    {
        $updated = Project::where('id', $id)->update([
            'isDeleted' => true,
            'updater' => Auth::id()
        ]);

        if ($updated) {
            $project = Project::findOrFail($id);
            $auth = Auth::user();
            $auth->logs()->create([
                'target' => 'project',
                'description' => "[SOFT DELETE] project {$project->name}",
            ]);
        }

        return redirect(route('project.list', ['project_owner_id' => $request->query('project_owner_id')]))
            ->with('warning', "Project '{$project->name}' berhasil dihapus!");
    }
}
