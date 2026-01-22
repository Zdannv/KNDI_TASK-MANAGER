<?php

namespace App\Http\Controllers;

use App\Models\ProjectOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clients = Client::where('isDeleted', false)->get();
        $users = User::get();
        return Inertia::render('Client/Index', compact('clients', 'users'));   
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

        $client = Client::create([
            'name' => $request->name,
            'creator' => Auth::id(),
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project owner',
            'description' => "[CREATE] project owner {$client->name}",
        ]);

        return redirect(route('client.list', absolute: false))->with('success', "Project owner '{$client->name}' berhasil dibuat!");
    }

    /**
     * Update the client's data.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $client = Client::findOrFail($id);
        $client->update([
            'name' => $request->name,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project owner',
            'description' => "[UPDATE] project owner {$client->name}",
        ]);

        return redirect(route('client.list', absolute: false))->with('success', "Project owner '{$client->name}' berhasil diperbarui!");
    }

    /**
     * Delete the client's data.
     */
    public function destroy($id): RedirectResponse
    {
        $client = Client::findOrFail($id);
        $client->update([
            'isDeleted' => true,
            'updater' => Auth::id()
        ]);

        Auth::user()->logs()->create([
            'target' => 'project owner',
            'description' => "[SOFT DELETE] project owner {$client->name}",
        ]);

        return redirect(route('client.list', absolute: false))->with('warning', "Project owner '{$client->name}' berhasil dihapus!");
    }
}
