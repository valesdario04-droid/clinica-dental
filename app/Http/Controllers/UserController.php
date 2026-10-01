<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::with('role')
            ->orderBy('name')
            ->paginate(10);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();

        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'active' => $request->boolean('active'),
        ]);

        $data = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'digits:9'],
            'active' => ['boolean'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        User::create($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->merge([
            'active' => $request->boolean('active'),
        ]);

        $data = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone' => ['required', 'digits:9'],
            'active' => ['boolean'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        if ($user->id === auth()->id() && ! $data['active']) {
            return back()
                ->withInput()
                ->withErrors(['active' => 'No puedes desactivar tu propia cuenta.']);
        }

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'No puedes desactivar tu propia cuenta.');
        }

        $user->update(['active' => false]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuario desactivado correctamente.');
    }
}