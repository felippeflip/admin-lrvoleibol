<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;
use Spatie\Permission\Models\Role;

class ProfileUserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->paginate(20);
        $profiles = Profile::orderBy('name')->get();
        return view('profile_user.index', compact('users', 'profiles'));
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
