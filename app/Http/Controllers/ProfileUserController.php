<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ProfileUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('user')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%')
                  ->orWhere('email', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('id', $request->role);
            });
        }

        $users = $query->paginate(20)->appends($request->all());
        $roles = Role::orderBy('name')->get();
        $profiles = Profile::orderBy('name')->get(); // Mantendo caso ainda seja usado em outro lugar, mas o foco é role

        return view('profile_user.index', [
            'users' => $users,
            'roles' => $roles,
            'profiles' => $profiles
        ]);
    }

    public function create()
    {
        $users = User::all();
        $roles = Role::all();
        return view('profile_user.create', compact('users', 'roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->assignRole($request->role);

        return redirect()->route('profile_user.index')->with('success', 'Perfil associado ao usuário com sucesso.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('profile_user.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($id);
        $user->syncRoles([$request->role]);

        return redirect()->route('profile_user.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach();

        return redirect()->route('profile_user.index')->with('success', 'Perfil removido do usuário com sucesso.');
    }
}
