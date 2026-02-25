<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Task;
use App\Jobs\ProcessFaceRecognition;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \Cache::remember('users_page_' . request('page', 1), 1800, function() {
            return User::orderBy('role', 'desc')
                ->paginate(10)
                ->withQueryString();
        });

        return Inertia::render('User/Index', [
            'users' => $users
        ]);   
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
            'face_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'password' => Hash::make($request->password),
            'face_embedding' => null
        ]);

        if ($request->hasFile('face_photo')) {
            $path = $request->file('face_photo')->store('temp_faces');
            ProcessFaceRecognition::dispatch($user, $path);
        }

        \Cache::flush();

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
            'face_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ];

        if ($request->filled('password')) {
            $rules['password'] = ['required', 'confirmed', Rules\Password::defaults()];
        }

        $validated = $request->validate($rules);

        $user = User::findOrFail($id);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        if ($request->hasFile('face_photo')) {
            $path = $request->file('face_photo')->store('temp_faces');
            ProcessFaceRecognition::dispatch($user, $path);
        }

        \Cache::flush();

        Auth::user()->logs()->create([
            'target' => 'user',
            'description' => "[UPDATE] user {$user->name}",
        ]);

        return redirect(route('user.list', absolute: false))
            ->with('success', "User '{$user->name}' berhasil diperbarui! Foto sedang diproses di background.");
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
            ->exists();

        if ($user->id === Auth::id()) {
            return back()->with('error', "Tidak dapat menghapus akun sendiri!");
        }

        if ($hasActiveTasks) {
            return back()->with('error', "User masih memiliki tugas yang masih aktif!");
        }

        $user->delete();

        \Cache::flush();

        Auth::user()->logs()->create([
            'target' => 'user',
            'description' => "[DELETE] user {$user->name}",
        ]);

        return redirect(route('user.list', absolute: false))->with('warning', "User '{$user->name}' berhasil dihapus!");
    }
}