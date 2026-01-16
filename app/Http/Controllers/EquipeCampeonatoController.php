<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campeonato;
use App\Models\Equipe;
use App\Models\Time;
use App\Models\Categoria;
use Illuminate\Support\Facades\Log;

class EquipeCampeonatoController extends Controller
{
    /**
     * Exibe a lista de Equipe inscritas em um campeonato específico.
     */
    public function index(Request $request, Campeonato $campeonato)
    {
        // Carrega as Equipe associadas a este campeonato, com seus respectivos times e categorias.
        $query = $campeonato->equipes()->with(['time', 'categoria']);

        // Filtro por Nome da Equipe
        if ($request->filled('search_equipe')) {
            $query->where('eqp_nome', 'like', '%' . $request->search_equipe . '%');
        }

        // Filtro por Time (Select)
        if ($request->filled('search_time')) {
            $query->where('tim_fk_id', $request->search_time);
        }

        // Filtro por Categoria (Select)
        if ($request->filled('search_categoria')) {
            $query->where('cto_fk_id', $request->search_categoria);
        }

        // Filtro por Nome do Treinador
        if ($request->filled('search_treinador')) {
            $query->where('eqp_nome_treinador', 'like', '%' . $request->search_treinador . '%');
        }

        $equipes = $query->paginate(10)->appends($request->all());

        // Carregar opções para os selects de filtro
        $times = Time::orderBy('tim_nome')->get();
        $categorias = Categoria::orderBy('cto_nome')->get();

        // Passa o objeto do campeonato e as equipes para a view.
        return view('equipes_campeonato.index', compact('equipes', 'campeonato', 'times', 'categorias'));
    }

    /**
     * Exibe o formulário para adicionar equipes a um campeonato.
     */
    public function create(Campeonato $campeonato)
    {
        // Verifica se o campeonato está ativo
        if (!$campeonato->cpo_ativo) {
            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)
                ->withErrors(['error' => 'Este campeonato está encerrado/inativo e não permite mais alterações de equipes.']);
        }
        // Carrega as equipes já inscritas no campeonato
        $equipesInscritas = $campeonato->equipes()->with(['time', 'categoria'])->get();
        $equipesJaInscritasIds = $equipesInscritas->pluck('eqp_id');

        // Inicia a query para buscar equipes não inscritas
        $query = Equipe::whereNotIn('eqp_id', $equipesJaInscritasIds)
            ->with('time', 'categoria');

        // Se o usuário for responsável por time, filtra apenas as equipes dele
        $user = auth()->user();

        // Verifica se o usuário tem a flag de responsável por time.
        // Se for admin, assume-se que pode ver tudo (admin geralmente não tem is_resp_time=true, ou a lógica permite ver tudo se não entrar no if)
        // Caso queira também checar role: || $user->hasRole('ResponsavelTime')
        // Se não for Administrador e tiver permissão de responsável, filtra pelo time
        if (!$user->hasRole('Administrador') && ($user->is_resp_time || $user->hasRole('ResponsavelTime'))) {
            $query->whereHas('time', function ($q) use ($user) {
                $q->where('tim_user_id', $user->id);
            });
        }

        $equipesDisponiveis = $query->orderBy('eqp_nome_detalhado')->get();

        // Passa as duas listas de equipes para a view
        return view('equipes_campeonato.create', compact('campeonato', 'equipesDisponiveis', 'equipesInscritas'));
    }

    /**
     * Armazena as equipes selecionadas para um campeonato.
     */
    public function store(Request $request, Campeonato $campeonato)
    {
        // Verifica se o campeonato está ativo
        if (!$campeonato->cpo_ativo) {
            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)
                ->withErrors(['error' => 'Campeonato inativo. Não é possível alterar as equipes.']);
        }
        $request->validate([
            'equipe_ids' => 'nullable|array', // Agora pode ser null se nada for selecionado
            'equipe_ids.*' => 'exists:equipes,eqp_id',
            'eqp_cpo_dt_inscricao' => 'nullable|date',
            // 'eqp_cpo_classificacaofinal' => 'nullable|integer' (adicionar se necessário)
        ]);

        try {
            $dataToSync = [];
            if ($request->has('equipe_ids')) {
                foreach ($request->equipe_ids as $equipeId) {
                    $dataToSync[$equipeId] = [
                        'eqp_cpo_dt_inscricao' => $request->input('eqp_cpo_dt_inscricao', now()),
                    ];
                }
            }
            // Usa sync para remover as equipes que não foram selecionadas e adicionar as novas
            $campeonato->equipes()->sync($dataToSync);

            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)->with('success', 'Equipes atualizadas no campeonato com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar equipes no campeonato: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao atualizar equipes no campeonato.'])->withInput();
        }
    }

    /**
     * Remove uma equipe de um campeonato específico.
     */
    public function destroy(Campeonato $campeonato, Equipe $equipe)
    {
        // Verifica se o campeonato está ativo
        if (!$campeonato->cpo_ativo) {
            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)
                ->withErrors(['error' => 'Campeonato inativo. Não é possível remover equipes.']);
        }
        try {
            // Remove a associação da equipe com o campeonato na tabela pivot
            $campeonato->equipes()->detach($equipe->eqp_id);

            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)->with('success', 'Equipe removida do campeonato com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao remover equipe do campeonato: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao remover equipe do campeonato.']);
        }
    }
    /**
     * Retorna a lista de equipes inscritas em um campeonato em formato JSON.
     * Usado no formulário de criação de jogos.
     */
    public function listByCampeonatoJson($campeonatoId)
    {
        $equipes = \App\Models\EquipeCampeonato::where('cpo_fk_id', $campeonatoId)
            ->with(['equipe'])
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->eqp_cpo_id, // ID da tabela pivot
                    'nome' => $item->equipe->eqp_nome_detalhado ?? 'Time Sem Nome'
                ];
            });

        return response()->json($equipes);
    }
}
