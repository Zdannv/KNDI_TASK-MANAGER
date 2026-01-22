<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::orderBy('role', 'asc')->get();
        return Inertia::render('User/Index', compact('users'));   
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
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'role' => ['required', 'in:other,pm,pg,co,ds'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
        ]);

        Auth::user()->logs()->create([
            'target' => 'user',
            'description' => "[CREATE] user {$user->name}",
        ]);

        return redirect(route('user.list', absolute: false))->with('success', "User '{$user->name}' berhasil dibuat!");
    }

    /**
     * Update the user's account.
     */
    public function update(Request $request, $id): RedirectResponse
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:users,email,' . $id,
            'role' => ['required', 'in:other,pm,pg,co,ds'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $validated = $request->validate($rules);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user = User::findOrFail($id);
        $user->update($updateData);
        Auth::user()->logs()->create([
            'target' => 'user',
            'description' => "[UPDATE] user {$user->name}",
        ]);

        return redirect(route('user.list', absolute: false))->with('success', "User '{$user->name}' berhasil diperbarui!");
    }

    /**
     * Delete the user's account.
     */
    public function destroy($id): RedirectResponse
    {
        $user = User::findOrFail($id);

        $hasActiveTasks = Task::where('isActive', true)
            ->where(function ($query) use ($id) {
                $query->whereJsonContains('programmer', $id)
                  ->orWhereJsonContains('designer', $id)
                  ->orWhereJsonContains('communicator', $id)
                  ->orWhere('pl', $id);
            })
            ->exist();

        if ($user->id === Auth::id()) {
            return back()->with('error', "Tidak dapat menghapus akun sendiri!");
        }

        if ($hasActiveTasks) {
            return back()->with('error', "User masih memiliki tugas yang masih aktif!");
        }

        $user->delete();

        Auth::user()->logs()->create([
            'target' => 'user',
            'description' => "[DELETE] user {$user->name}",
        ]);

        return redirect(route('user.list', absolute: false))->with('warning', "User '{$user->name}' berhasil dihapus!");
    }
}
