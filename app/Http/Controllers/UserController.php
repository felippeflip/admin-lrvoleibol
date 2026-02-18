<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Time;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Filtro de Busca (Nome ou Apelido)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('apelido', 'like', "%{$search}%");
            });
        }

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
        $times = Time::where('tim_status', 1)->orderBy('tim_nome')->get();
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'Responsável pelo Time')->get();
        return view('users.create', compact('times', 'roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'cref' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:50',
            'cep' => 'nullable|string|max:10',
            'rg' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'lrv' => 'nullable|string|max:20',
            'role' => 'nullable|exists:roles,name', // Validar Role
            'tipo_arbitro' => 'nullable|string|max:50',
            'time_id' => 'nullable|exists:times,tim_id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // --- Processamento do Upload da Foto ---
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            try {
                $path = Storage::disk('user_fotos')->putFileAs('/', $file, $filename);
                $validated['foto'] = $filename;
            } catch (\Exception $e) {
                Log::error("Erro ao salvar a foto do usuário: " . $e->getMessage());
                return redirect()->back()->with('error', 'Erro ao salvar a foto.');
            }
        }

        $user = User::create($validated);
        
        // Se for Comissão Técnica, o time_id deve vir no request e ser salvo no usuário
        // O $validated já permite time_id, então o create acima já deve ter pego se veio no request.
        // Apenas para garantir que não haja sobrescrita ou lógica extra necessária.
        // O time_id está no user table? Sim, user->time_id.
        // O create($validated) já resolveu isso se 'time_id' estiver no fillable do User e no $validated.
        // (Já verificado: time_id está no validated)

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
        $times = Time::where('tim_status', 1)->orderBy('tim_nome')->get();
        $roles = \Spatie\Permission\Models\Role::where('name', '!=', 'Responsável pelo Time')->get();
        return view('users.edit', compact('user', 'times', 'roles'));
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'apelido' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telefone' => 'nullable|string|max:20',
            'cpf' => 'nullable|string|max:14',
            'cref' => 'nullable|string|max:20',
            'endereco' => 'nullable|string|max:255',
            'numero' => 'nullable|string|max:20',
            'bairro' => 'nullable|string|max:255',
            'cidade' => 'nullable|string|max:255',
            'estado' => 'nullable|string|max:50',
            'cep' => 'nullable|string|max:10',
            'rg' => 'nullable|string|max:20',
            'data_nascimento' => 'nullable|date',
            'lrv' => 'nullable|string|max:20',
            'role' => 'nullable|exists:roles,name',
            'tipo_arbitro' => 'nullable|string|max:50',
            'time_id' => 'nullable|exists:times,tim_id',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
        ]);

        // --- Processamento do Upload da Foto ---
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();

            try {
                if ($user->foto && Storage::disk('user_fotos')->exists($user->foto)) {
                    Storage::disk('user_fotos')->delete($user->foto);
                }
                $path = Storage::disk('user_fotos')->putFileAs('/', $file, $filename);
                $validated['foto'] = $filename;
            } catch (\Exception $e) {
                Log::error("Erro ao atualizar foto do usuário: " . $e->getMessage());
                return redirect()->back()->with('error', 'Erro ao salvar a foto.');
            }
        }

        $user->update($validated);

        // Sincronizar Role
        if ($request->filled('role')) {
            $user->syncRoles([$request->role]);
        } else {
            $user->syncRoles([]);
        }

        // Lógica Responsável Time
        if ($request->role === 'ResponsavelTime' && $request->has('time_id')) {
            $time = Time::find($request->time_id);
            if ($time) {
                // Remove existing responsible if any
                Time::where('tim_user_id', $user->id)->update(['tim_user_id' => null]);
                $time->tim_user_id = $user->id;
                $time->save();
            }
        } elseif ($request->role !== 'ResponsavelTime') {
            // Se mudou de role e deixou de ser responsável, remove vínculo na tabela times
            Time::where('tim_user_id', $user->id)->update(['tim_user_id' => null]);
        }
        
        // Se for Juiz, o time_id já foi salvo via $validated no update() pois está no fillable.

        // Lógica Legada Arbitro
        $user->is_arbitro = ($request->role === 'Juiz');
        $user->save();

        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    public function destroy(User $user)
    {
        if ($user->foto && Storage::disk('user_fotos')->exists($user->foto)) {
            Storage::disk('user_fotos')->delete($user->foto);
        }
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
