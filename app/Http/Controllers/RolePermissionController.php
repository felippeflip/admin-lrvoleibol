<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('role-permission.index', compact('roles', 'permissions'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('role-permission.create', compact('permissions'));
    }
    

    public function store(Request $request)
    {
        // Valida os dados recebidos
        $request->validate([
            'role_name' => 'required|string|max:255',
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ]);
    
        // Cria a nova função
        $role = Role::create(['name' => $request->role_name]);
    
        // Atribui as permissões à função, se existirem
        if ($request->permissions) {
            $role->givePermissionTo($request->permissions);
        }
    
        return redirect()->route('role-permission.index');
    }

    public function edit(Role $role)
{
    $permissions = Permission::all();
    return view('role-permission.edit', compact('role', 'permissions'));
}

    
public function update(Request $request, Role $role)
{
    // Valida os dados recebidos
    $request->validate([
        'permissions' => 'array',
        'permissions.*' => 'string|exists:permissions,name',
    ]);

    // Sincroniza as permissões com a função
    $role->syncPermissions($request->permissions);

    return redirect()->route('role-permission.index')->with('success', 'Função atualizada com sucesso.');
}




    public function destroy(Role $role)
    {
        $role->delete();

        return redirect()->route('role-permission.index');
    }
}
