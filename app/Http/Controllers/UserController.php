<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro de Status
        $status = $request->input('status', 'active');
        if ($status === 'active') {
            $query->where('active', true);
        } elseif ($status === 'inactive') {
            $query->where('active', false);
        }

        // Filtro de Perfil
        if ($request->filled('role')) {
            $role = $request->role;
            if ($role === 'Administrador') {
                $query->role('Administrador');
            } elseif ($role === 'ResponsavelTime') {
                $query->role('ResponsavelTime');
            } elseif ($role === 'Juiz') {
                $query->where('is_arbitro', true);
            }
        }

        $users = $query->paginate(10)->appends($request->all());
        return view('users.index', compact('users'));
    }

    public function toggleStatus(User $user)
    {
        $user->active = !$user->active;
        $user->save();

        $statusMessage = $user->active ? 'ativado' : 'desativado';
        return redirect()->route('users.index')->with('success', "Usuário {$statusMessage} com sucesso.");
    }

    public function create()
    {
        $times = Time::all();
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.create', compact('times', 'roles'));
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
            'role' => 'nullable|exists:roles,name', // Validar Role
            'tipo_arbitro' => 'nullable|string|max:50',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Remover campos que não existem na tabela users se necessário, ou garantir que o fillable os ignore
        // is_arbitro e is_resp_time podem ser removidos do validate se não usados mais no DB
        // Mas por compatibilidade com banco legado, talvez mantê-los como false ou null

        $user = User::create($validated);

        // Atribuir Role
        if ($request->filled('role')) {
            $user->assignRole($request->role);
        }

        // Lógica de Responsável pelo Time usando a Role
        if ($request->role === 'ResponsavelTime' && $request->has('time_id')) {
            $time = Time::find($request->time_id);
            if ($time) {
                // Limpar responsabilidades anteriores se necessário (um time só tem um user responsável, e um user só um time)
                Time::where('tim_user_id', $user->id)->update(['tim_user_id' => null]);
                $time->tim_user_id = $user->id;
                $time->save();
            }
        }

        // Lógica Legada de Arbitro (para manter compatibilidade com banco se colunas existirem)
        if ($request->role === 'Juiz') {
            $user->is_arbitro = true;
            $user->save();
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $times = Time::all();
        $roles = \Spatie\Permission\Models\Role::all();
        return view('users.edit', compact('user', 'times', 'roles'));
    }

    public function update(Request $request, User $user)
    {
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
            'role' => 'nullable|exists:roles,name',
            'tipo_arbitro' => 'nullable|string|max:50',
        ]);

        $user->update($validated);

        // Sincronizar Role
        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        } else {
            // Se nenhum role enviado, remove todos? Ou mantém? 
            // Geralmente select vazio = remover roles
            $user->syncRoles([]);
        }

        // Lógica Responsável Time
        if ($request->role === 'ResponsavelTime' && $request->has('time_id')) {
            $time = Time::find($request->time_id);
            if ($time) {
                Time::where('tim_user_id', $user->id)->update(['tim_user_id' => null]);
                $time->tim_user_id = $user->id;
                $time->save();
            }
        } else {
            // Se não for mais responsável, remove vínculo
            Time::where('tim_user_id', $user->id)->update(['tim_user_id' => null]);
        }

        // Lógica Legada Arbitro
        $user->is_arbitro = ($request->role === 'Juiz');
        $user->save();

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
