<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Time;
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
        $times = Time::all();
        return view('users.create', compact('times'));
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
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'cep' => 'nullable|string|max:10',
            'is_arbitro' => 'boolean',
            'tipo_arbitro' => 'nullable|string|max:50',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        if ($request->has('is_responsavel') && $request->is_responsavel == 1 && $request->has('time_id')) {
            $time = Time::find($request->time_id);
            if ($time) {
                $time->tim_user_id = $user->id;
                $time->save();
            }
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $times = Time::all();
        return view('users.edit', compact('user', 'times'));
    }

    public function update(Request $request, User $user)
    {

        // dd($request); die;
        $validated = $request->validate([
            'name' => 'required|string|max:50',
            'apelido' => 'nullable|string|max:50',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telefone' => 'nullable|string|max:15',
            'cpf' => 'nullable|string|max:14',
            'cref' => 'nullable|string|max:10',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:80',
            'cidade' => 'nullable|string|max:50',
            'estado' => 'nullable|string|max:50',
            'cep' => 'nullable|string|max:10',
            'is_arbitro' => 'boolean',
            'tipo_arbitro' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        if ($request->has('is_responsavel') && $request->is_responsavel == 1 && $request->has('time_id')) {
            // Remove responsibility from other teams if necessary (optional, depending on business rule)
            // For now, just set the new team
            $time = Time::find($request->time_id);
            if ($time) {
                // If this user was responsible for another team, we might want to clear it?
                // Assuming one user can be responsible for multiple teams is NOT the case here based on the request "qual time será".
                // But let's stick to the request: "select which team".

                // First, clear previous responsibility for this user if we want to enforce 1 team per user? 
                // The request says "o usuário será o responsavel por um determinado TIME" (singular).
                // Let's clear any team where this user is currently responsible, just to be safe/clean?
                // Or maybe the user can be responsible for multiple? "um determinado TIME" implies one.

                // Let's clear previous responsibilities for this user to be safe.
                Time::where('tim_user_id', $user->id)->update(['tim_user_id' => null]);

                $time->tim_user_id = $user->id;
                $time->save();
            }
        } elseif (!$request->has('is_responsavel') || $request->is_responsavel == 0) {
            // If user is NOT responsible anymore, clear their teams
            Time::where('tim_user_id', $user->id)->update(['tim_user_id' => null]);
        }

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'Usuário deletado com sucesso.');
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::findOrFail($id);
        $user->password = Hash::make($request->input('password'));
        $user->save();

        return redirect()->route('users.index', $id)->with('success', 'Senha redefinida com sucesso.');
    }


}
