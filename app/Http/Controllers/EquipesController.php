<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipe; // Seu modelo Equipes
use App\Models\Time; // Para buscar Times para dropdowns (create/edit)
use App\Models\Categoria; // Para buscar Categorias para dropdowns (create/edit), se 'Categorias' for o nome do seu modelo
use Illuminate\Support\Facades\Log; // Para logs de erro

class EquipesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Carrega as equipes e EAGER LOADING (com) os modelos relacionados Time e Categoria
        // Isso evita o problema de N+1 queries e permite acessar $equipe->time->tim_nome na view
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if ($time) {
                $equipes = Equipe::where('eqp_time_id', $time->tim_id)->with(['time', 'categoria'])->paginate(10);
            } else {
                $equipes = Equipe::where('eqp_id', 0)->paginate(10);
            }
        } else {
            $equipes = Equipe::with(['time', 'categoria'])->paginate(10); // Adicione paginação se desejar
        }
        return view('equipes.index', compact('equipes'));
    }

    /**
     * Display a listing of the resource filtered by a specific time.
     */
    public function indexForTime(Time $time)
    {
        // Carrega as equipes com seus respectivos times e categorias para o time fornecido
        $equipes = Equipe::where('eqp_time_id', $time->tim_id)->with(['time', 'categoria'])->paginate(10);
        
        // Passa o objeto do time para a view para customizar o header
        return view('equipes.index', compact('equipes', 'time'));
    }

   
    /**
     * Show the form for creating a new resource.
     * Recebe um time_id opcional para pré-selecionar no dropdown.
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
             $times = Time::where('tim_user_id', $user->id)->get();
        } else {
            if ($request->has('time_id')) {
                $times = Time::where('tim_id', $request->query('time_id'))->get();
            } else {
                $times = Time::orderBy('tim_nome')->get();
            }
        }
        $categorias = Categoria::orderBy('cto_nome')->get();
        $timeId = $request->query('time_id'); // Pega o time_id da URL

        return view('equipes.create', compact('times', 'categorias', 'timeId'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time) {
                 return redirect()->back()->withErrors(['error' => 'Você não possui um time vinculado.']);
            }
            $request->merge(['eqp_time_id' => $time->tim_id]);
        }
        $request->validate([
            'eqp_time_id' => 'required|exists:times,tim_id',
            'eqp_categoria_id' => 'required|exists:categorias,cto_id', // cto_id é a PK da sua tabela categorias
            'eqp_nome_detalhado' => 'required|string|max:50',
            'eqp_nome_treinador' => 'nullable|string|max:50',
        ]);

        try {
            Equipe::create($request->all());
            return redirect()->route('equipes.index')->with('success', 'Equipe criada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao criar equipe: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao criar equipe.'])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Equipe $equipe) // Injeção de modelo para facilitar
    {
        // Carrega os relacionamentos para a view show
        $equipe->load(['time', 'categoria']);
        return view('equipes.show', compact('equipe'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Equipe $equipe) // Injeção de modelo para facilitar
    {
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
             $times = Time::where('tim_user_id', $user->id)->get();
             // Security check: ensure the equipe belongs to the user's time
             if ($equipe->time->tim_user_id != $user->id) {
                 abort(403);
             }
        } else {
            $times = Time::orderBy('tim_nome')->get();
        }
        $categorias = Categoria::orderBy('cto_nome')->get(); // Assumindo 'cto_nome'
        return view('equipes.edit', compact('equipe', 'times', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Equipe $equipe) // Injeção de modelo para facilitar
    {
        $user = auth()->user();
        if ($user->hasRole('ResponsavelTime') && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();
            if (!$time || $equipe->eqp_time_id != $time->tim_id) {
                 abort(403);
            }
            $request->merge(['eqp_time_id' => $time->tim_id]);
        }
        $request->validate([
            'eqp_time_id' => 'required|exists:times,tim_id',
            'eqp_categoria_id' => 'required|exists:categorias,cto_id',
            'eqp_nome_detalhado' => 'required|string|max:50',
            'eqp_nome_treinador' => 'nullable|string|max:50',
        ]);

        try {
            $equipe->update($request->all());
            return redirect()->route('equipes.index')->with('success', 'Equipe atualizada com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar equipe: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar equipe.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Equipe $equipe) // Injeção de modelo para facilitar
    {
        try {
            $equipe->delete();
            return redirect()->route('equipes.index')->with('success', 'Equipe excluída com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao excluir equipe: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao excluir equipe.']);
        }
    }
}