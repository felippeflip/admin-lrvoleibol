<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campeonatos;
use App\Models\Equipes;
use Illuminate\Support\Facades\Log;

class EquipeCampeonatoController extends Controller
{
    /**
     * Exibe a lista de equipes inscritas em um campeonato específico.
     */
    public function index(Campeonatos $campeonato)
    {
        // Carrega as equipes associadas a este campeonato, com seus respectivos times e categorias.
        $equipes = $campeonato->equipes()->with(['time', 'categoria'])->paginate(10);
        
        // Passa o objeto do campeonato e as equipes para a view.
        return view('equipes_campeonato.index', compact('equipes', 'campeonato'));
    }

    /**
     * Exibe o formulário para adicionar equipes a um campeonato.
     */
    public function create(Campeonatos $campeonato)
    {
        // Carrega as equipes já inscritas no campeonato
        $equipesInscritas = $campeonato->equipes()->with(['time', 'categoria'])->get();
        $equipesJaInscritasIds = $equipesInscritas->pluck('eqp_id');

        // Busca todas as equipes que NÃO ESTÃO inscritas neste campeonato
        $equipesDisponiveis = Equipes::whereNotIn('eqp_id', $equipesJaInscritasIds)
                                     ->with('time', 'categoria')
                                     ->orderBy('eqp_nome_detalhado')
                                     ->get();

        // Passa as duas listas de equipes para a view
        return view('equipes_campeonato.create', compact('campeonato', 'equipesDisponiveis', 'equipesInscritas'));
    }

    /**
     * Armazena as equipes selecionadas para um campeonato.
     */
    public function store(Request $request, Campeonatos $campeonato)
    {
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
    public function destroy(Campeonatos $campeonato, Equipes $equipe)
    {
        try {
            // Remove a associação da equipe com o campeonato na tabela pivot
            $campeonato->equipes()->detach($equipe->eqp_id);
            
            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)->with('success', 'Equipe removida do campeonato com sucesso!');
        } catch (\Exception $e) {
            Log::error('Erro ao remover equipe do campeonato: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao remover equipe do campeonato.']);
        }
    }
}
