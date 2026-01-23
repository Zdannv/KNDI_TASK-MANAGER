<?php

namespace App\Http\Controllers;

use App\Models\ProjectOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projectOwners = ProjectOwner::where('isDeleted', false)->get();
        $users = User::get();
        return Inertia::render('ProjectOwner/Index', compact('projectOwners', 'users'));   
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
        ]);

        $projectOwner = ProjectOwner::create([
            'name' => $request->name,
            'creator' => Auth::id(),
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project owner',
            'description' => "[CREATE] project owner {$projectOwner->name}",
        ]);

        return redirect(route('client.list', absolute: false))->with('success', "Project owner '{$projectOwner->name}' berhasil dibuat!");
    }

    /**
     * Update the ProjectOwner's data.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $ProjectOwner = ProjectOwner::findOrFail($id);
        $ProjectOwner->update([
            'name' => $request->name,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project owner',
            'description' => "[UPDATE] project owner {$ProjectOwner->name}",
        ]);

        return redirect(route('client.list', absolute: false))->with('success', "Project owner '{$ProjectOwner->name}' berhasil diperbarui!");
    }

    /**
     * Delete the client's data.
     */
    public function destroy($id): RedirectResponse
    {
        $ProjectOwner = ProjectOwner::findOrFail($id);
        $ProjectOwner->update([
            'isDeleted' => true,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project owner',
            'description' => "[SOFT DELETE] project owner {$ProjectOwner->name}",
        ]);

        return redirect(route('client.list', absolute: false))->with('warning', "Project owner '{$ProjectOwner->name}' berhasil dihapus!");
    }
}
