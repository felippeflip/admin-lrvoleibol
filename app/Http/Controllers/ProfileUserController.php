<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Profile;
use App\Models\User;

class ProfileUserController extends Controller
{
    public function index()
    {
        $profiles = Profile::with('roles')->get();
        $users = User::with('profile')->get();
        return view('profile_user.index', compact('profiles', 'users'));
    }

    public function create()
    {
        $profiles = Profile::all();
        $users = User::all();
        return view('profile_user.create', compact('profiles', 'users'));
    }

    public function store(Request $request)
    {
        $user = User::findOrFail($request->user_id);
        $profile = Profile::findOrFail($request->profile_id);
        $user->profile()->associate($profile);
        $user->save();

        return redirect()->route('profile_user.index')->with('success', 'Perfil associado ao usuário com sucesso!');
    }

    public function edit(User $user)
    {
        $profiles = Profile::all();
        return view('profile_user.edit', compact('user', 'profiles'));
    }

    public function update(Request $request, User $user)
    {
        $profile = Profile::findOrFail($request->profile_id);
        $user->profile()->associate($profile);
        $user->save();

        return redirect()->route('profile_user.index')->with('success', 'Perfil atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        $user->profile()->dissociate();
        $user->save();

        return redirect()->route('profile_user.index')->with('success', 'Perfil removido do usuário com sucesso!');
    }
}
