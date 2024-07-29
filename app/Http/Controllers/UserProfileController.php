<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Spatie\Permission\Models\Role; 
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index()
    {
        $users = User::with('profile')->get(); // Ajuste para obter os usuários com seus perfis
        return view('profile_user.index', compact('users'));
    }

    public function create()
    {
        $profiles = Profile::all();
        return view('profile_user.create', compact('profiles'));
    }

    public function store(Request $request)
    {
        $user = User::find($request->user_id);
        $user->profile()->associate(Profile::find($request->profile_id));
        $user->save();
        return redirect()->route('profile_user.index');
    }

    public function edit(User $user)
    {
        $profiles = Profile::all();
        return view('profile_user.edit', compact('user', 'profiles'));
    }

    public function update(Request $request, User $user)
    {
        $user->profile()->associate(Profile::find($request->profile_id));
        $user->save();
        return redirect()->route('profile_user.index');
    }

    public function destroy(User $user)
    {
        $user->profile()->dissociate();
        $user->save();
        return redirect()->route('profile_user.index');
    }
}
