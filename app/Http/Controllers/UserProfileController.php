<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('profile');

        // Filtro por Usuário (Nome ou Email)
        if ($request->filled('user')) {
            $search = $request->input('user');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filtro por Role (já que a view agora usa 'role')
        if ($request->filled('role')) {
             $query->whereHas('roles', function($q) use ($request) {
                $q->where('id', $request->role);
            });
        }

        $users = $query->paginate(20)->appends($request->all());
        $roles = Role::orderBy('name')->get();
        $profiles = Profile::orderBy('name')->get();

        return view('profile_user.index', compact('users', 'roles', 'profiles'));
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
        return redirect()->route('profiles.index');
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
        return redirect()->route('profiles.index');
    }

    public function destroy(User $user)
    {
        $user->profile()->dissociate();
        $user->save();
        return redirect()->route('profiles.index');
    }
}
