<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(): View
    {
        $users = User::query()
            ->orderBy('name')
            ->get();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'lowercase', Rule::unique('app_users', 'email')],
            'role' => ['required', Rule::in(['admin', 'manager', 'engineer'])],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'active' => ['nullable', 'boolean'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password_hash' => Hash::make($validated['password']),
            'active' => (bool) ($validated['active'] ?? false),
        ]);

        return redirect()->route('admin.users.index')->with('status', 'User created.');
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'lowercase', Rule::unique('app_users', 'email')->ignore($user->id)],
            'role' => ['required', Rule::in(['admin', 'manager', 'engineer'])],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'active' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->active = (bool) ($validated['active'] ?? false);

        if (! empty($validated['password'])) {
            $user->password_hash = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User updated.');
    }

    /**
     * Toggle active status for the specified user.
     */
    public function toggleActive(User $user): RedirectResponse
    {
        $user->active = ! $user->active;
        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'User status updated.');
    }
}
