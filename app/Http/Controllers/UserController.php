<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telefone' => 'nullable|string|max:15',
            'cpf' => 'nullable|string|max:14',
            'cref' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:10',
            'is_arbitro' => 'boolean',
            'tipo_arbitro' => 'nullable|string|max:50',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {

       // dd($request); die;
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'apelido' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'telefone' => 'nullable|string|max:15',
            'cpf' => 'nullable|string|max:14',
            'cref' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'bairro' => 'nullable|string|max:80',
            'cidade' => 'nullable|string|max:50',
            'estado' => 'nullable|string|max:50',
            'cep' => 'nullable|string|max:10',
            'is_arbitro' => 'boolean',
            'tipo_arbitro' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário deletado com sucesso.');
    }
}
