<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public function index(Request $request)
    {
        $profiles = Profile::with('roles')->orderBy('name')->get();
        return view('profiles.index', compact('profiles'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('profiles.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:profiles,name',
            'enable_mobile_view' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $profile = Profile::create([
            'name' => $request->name,
            'enable_mobile_view' => $request->has('enable_mobile_view'),
        ]);

        if ($request->has('roles')) {
            $profile->roles()->sync($request->roles);
        }

        return redirect()->route('profiles.index')->with('success', 'Perfil criado com sucesso.');
    }

    public function edit(Profile $profile)
    {
        $roles = Role::all();
        return view('profiles.edit', compact('profile', 'roles'));
    }

    public function update(Request $request, Profile $profile)
    {
        $request->validate([
            'name' => 'required|string|unique:profiles,name,' . $profile->id,
            'enable_mobile_view' => 'nullable|boolean',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $profile->update([
            'name' => $request->name,
            'enable_mobile_view' => $request->has('enable_mobile_view'),
        ]);

        if ($request->has('roles')) {
            $profile->roles()->sync($request->roles);
        } else {
            $profile->roles()->detach();
        }

        return redirect()->route('profiles.index')->with('success', 'Perfil atualizado com sucesso.');
    }

    public function destroy(Profile $profile)
    {
        $profile->delete();
        return redirect()->route('profiles.index')->with('success', 'Perfil deletado com sucesso.');
    }
}
