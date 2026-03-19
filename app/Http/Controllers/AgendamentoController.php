<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campeonato;
use App\Models\Categoria;
use App\Models\EquipeCampeonato;
use App\Models\Jogo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AgendamentoController extends Controller
{
    /**
     * Admin: Render the form to manually define groups
     */
    public function definirGrupos($campeonato_id, $categoria_id)
    {
        $equipes = EquipeCampeonato::where('cpo_fk_id', $campeonato_id)
            ->whereHas('equipe', function($q) use ($categoria_id) {
                $q->where('eqp_categoria_id', $categoria_id);
            })->with('equipe.time')->get();

        $cmp = Campeonato::findOrFail($campeonato_id);
        $cat = Categoria::findOrFail($categoria_id);
        
        // Verifica se existem jogos para evitar acesso indevido se já gerados
        $pivotIds = $equipes->pluck('eqp_cpo_id')->toArray();
        $existingGames = Jogo::whereIn('jgo_eqp_cpo_mandante_id', $pivotIds)
                             ->orWhereIn('jgo_eqp_cpo_visitante_id', $pivotIds)
                             ->exists();

        if ($existingGames) {
            return redirect()->route('agendamentos.admin.index', $campeonato_id)->withErrors(['error' => 'Já existem jogos gerados (ou em andamento) para esta categoria.']);
        }

        return view('agendamentos.admin.definir_grupos', compact('equipes', 'cmp', 'cat'));
    }

    /**
     * Admin: Generate Schedule for a category in a championship
     */
    public function gerarAgendamento(Request $request, $campeonato_id, $categoria_id)
    {
        try {
            // Find pivot records for teams in this category within the championship
            $equipesPivot = EquipeCampeonato::where('cpo_fk_id', $campeonato_id)
                ->whereHas('equipe', function($q) use ($categoria_id) {
                    $q->where('eqp_categoria_id', $categoria_id);
                })->get();

            $N = $equipesPivot->count();

            if ($N < 2) {
                return redirect()->back()->withErrors(['error' => 'É necessário pelo menos 2 equipes para gerar jogos.']);
            }

            // Check if games already exist for this category in this championship to avoid duplicates
            // We can check if any Jogo exists where mandante is in the $equipesPivot
            $pivotIds = $equipesPivot->pluck('eqp_cpo_id')->toArray();
            $existingGames = Jogo::whereIn('jgo_eqp_cpo_mandante_id', $pivotIds)
                                 ->orWhereIn('jgo_eqp_cpo_visitante_id', $pivotIds)
                                 ->exists();

            if ($existingGames) {
                return redirect()->back()->withErrors(['error' => 'Já existem jogos gerados (ou em andamento) para esta categoria neste campeonato.']);
            }

            $jogosAGerar = [];

            if ($N <= 12) {
                // Rodízio Turno A e Turno B (Inversão)
                $jogosAGerar = array_merge(
                    $this->generateRoundRobin($pivotIds, 'Turno A'),
                    $this->generateRoundRobinReverse($pivotIds, 'Turno B')
                );
            } elseif ($N >= 13 && $N <= 15) {
                // Turno Único
                $jogosAGerar = $this->generateRoundRobin($pivotIds, 'Turno Único');
            } else {
                // N >= 16: Groups
                if (!$request->has('grupos')) {
                    // Redirect to the GET route where groups are defined
                    return redirect()->route('agendamentos.definirGrupos', [
                        'campeonato' => $campeonato_id,
                        'categoria' => $categoria_id
                    ]);
                }

                // If we get here, admin submitted the groups definitions
                $gruposInput = $request->input('grupos'); // arrado [eqp_cpo_id => 'Grupo A', ...]
                
                // Agrupar as equipes pelos valores 
                $gruposFormatados = [];
                foreach ($gruposInput as $pivotId => $nomeGrupo) {
                    if (!empty($nomeGrupo)) {
                        $gruposFormatados[$nomeGrupo][] = $pivotId;
                    }
                }

                if (count($gruposFormatados) < 2) {
                    return redirect()->back()->withErrors(['error' => 'É necessário formar pelo menos 2 grupos válidos.']);
                }

                foreach ($gruposFormatados as $nomeGrupo => $grupoPivotIds) {
                    // Turno A e Turno B (Inversão) para cada grupo
                    $jogosGrupoTurnoA = $this->generateRoundRobin($grupoPivotIds, "{$nomeGrupo} - Turno A");
                    $jogosGrupoTurnoB = $this->generateRoundRobinReverse($grupoPivotIds, "{$nomeGrupo} - Turno B");
                    $jogosAGerar = array_merge($jogosAGerar, $jogosGrupoTurnoA, $jogosGrupoTurnoB);
                }
            }

            // Insert games into database
            foreach ($jogosAGerar as $match) {
                Jogo::create([
                    'jgo_eqp_cpo_mandante_id' => $match['mandante'],
                    'jgo_eqp_cpo_visitante_id' => $match['visitante'],
                    'jgo_fase' => $match['fase'],
                    'jgo_status_agendamento' => 'pendente_preenchimento',
                    // Let the rest be null, wait for team to fill
                ]);
            }

            return redirect()->route('agendamentos.admin.index', $campeonato_id)
                             ->with('success', 'Agendamento prévio gerado com sucesso para ' . $N . ' equipes.');

        } catch (\Exception $e) {
            Log::error('Erro ao gerar agendamento: ' . $e->getMessage());
            return redirect()->route('agendamentos.admin.index', $campeonato_id)
                             ->withErrors(['error' => 'Ocorreu um erro ao gerar o agendamento.']);
        }
    }

    private function generateRoundRobin(array $equipes, string $fase)
    {
        $matches = [];
        $count = count($equipes);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $matches[] = [
                    'mandante' => $equipes[$i],
                    'visitante' => $equipes[$j],
                    'fase' => $fase,
                ];
            }
        }
        return $matches;
    }

    private function generateRoundRobinReverse(array $equipes, string $fase)
    {
        $matches = [];
        $count = count($equipes);
        for ($i = 0; $i < $count; $i++) {
            for ($j = $i + 1; $j < $count; $j++) {
                $matches[] = [
                    'mandante' => $equipes[$j], // inverted
                    'visitante' => $equipes[$i],
                    'fase' => $fase,
                ];
            }
        }
        return $matches;
    }

    // List generated schedule (Admin perspective)
    public function indexAdmin(Request $request, $campeonato)
    {
        $cmp = Campeonato::findOrFail($campeonato);
        $query = Jogo::with(['mandante.equipe.time', 'visitante.equipe.time', 'mandante.equipe.categoria', 'ginasio'])
            ->whereNotNull('jgo_status_agendamento')
            ->whereHas('mandante', function ($q) use ($campeonato) {
                $q->where('cpo_fk_id', $campeonato);
            });

        // Filtro: Categoria
        if ($request->filled('categoria')) {
            $catId = $request->categoria;
            $query->whereHas('mandante.equipe', function($q) use ($catId) {
                $q->where('eqp_categoria_id', $catId);
            });
        }

        // Filtro: Fase
        if ($request->filled('fase')) {
            $query->where('jgo_fase', $request->fase);
        }

        // Filtro: Status
        if ($request->filled('status')) {
            $query->where('jgo_status_agendamento', $request->status);
        }

        // Filtro: Mandante
        if ($request->filled('mandante')) {
            $term = $request->mandante;
            $query->whereHas('mandante.equipe', function($q) use ($term) {
                $q->where('eqp_nome_detalhado', 'like', "%{$term}%");
            });
        }

        // Filtro: Visitante
        if ($request->filled('visitante')) {
            $term = $request->visitante;
            $query->whereHas('visitante.equipe', function($q) use ($term) {
                $q->where('eqp_nome_detalhado', 'like', "%{$term}%");
            });
        }

        $jogos = $query->paginate(50)->appends($request->all());

        $categorias = \App\Models\Categoria::whereHas('equipes.campeonatos', function($q) use ($campeonato) {
            $q->where('cpo_fk_id', $campeonato);
        })->get();

        foreach ($categorias as $cat) {
            $cat->qtd_equipes = EquipeCampeonato::where('cpo_fk_id', $campeonato)
                ->whereHas('equipe', function($q) use ($cat) {
                    $q->where('eqp_categoria_id', $cat->cto_id);
                })->count();
        }

        $fases = Jogo::whereHas('mandante', function ($q) use ($campeonato) {
            $q->where('cpo_fk_id', $campeonato);
        })->whereNotNull('jgo_status_agendamento')
          ->whereNotNull('jgo_fase')
          ->where('jgo_fase', '!=', '')
          ->select('jgo_fase')->distinct()->pluck('jgo_fase');

        return view('agendamentos.admin.index', compact('jogos', 'cmp', 'categorias', 'fases'));
    }

    public function deletarAgendamento($jogo_id)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado.']);
        }
        
        $jogo = Jogo::findOrFail($jogo_id);
        
        // Remove also from Wordpress if it was already synced
        if ($jogo->jgo_status_agendamento == 'aprovado' && $jogo->jgo_wp_id) {
            $wpService = new \App\Services\WordpressGameService();
            $wpService->delete($jogo->jgo_wp_id);
        }
        
        $jogo->delete();
        
        return redirect()->back()->with('success', 'Agendamento prévio deletado com sucesso.');
    }

    public function deletarMassa(Request $request)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado.']);
        }
        
        $idsStr = $request->input('jogos_ids');
        if (empty($idsStr)) {
            return redirect()->back()->withErrors(['error' => 'Nenhum agendamento selecionado.']);
        }
        
        $idsArray = explode(',', $idsStr);
        $jogos = Jogo::whereIn('jgo_id', $idsArray)->get();
        
        $wpService = new \App\Services\WordpressGameService();
        
        $countDeletados = 0;
        foreach ($jogos as $jogo) {
            // Remove also from Wordpress if it was already synced
            if ($jogo->jgo_status_agendamento == 'aprovado' && $jogo->jgo_wp_id) {
                $wpService->delete($jogo->jgo_wp_id);
            }
            $jogo->delete();
            $countDeletados++;
        }
        
        return redirect()->back()->with('success', $countDeletados . ' agendamento(s) deletado(s) com sucesso.');
    }

    public function aprovarMassa(Request $request)
    {
        if (!auth()->user()->hasRole('Administrador')) {
            return redirect()->back()->withErrors(['error' => 'Acesso negado.']);
        }
        
        $idsStr = $request->input('jogos_ids');
        if (empty($idsStr)) {
            return redirect()->back()->withErrors(['error' => 'Nenhum agendamento selecionado.']);
        }
        
        $idsArray = explode(',', $idsStr);
        $jogos = Jogo::with(['mandante.campeonato', 'mandante.equipe.categoria'])
                     ->whereIn('jgo_id', $idsArray)
                     ->where('jgo_status_agendamento', 'pendente_aprovacao')
                     ->get();
                     
        if ($jogos->isEmpty()) {
            return redirect()->back()->withErrors(['error' => 'Nenhum agendamento válido selecionado (apenas jogos "Aguardando Aprovação" podem ser aprovados).']);
        }
        
        $wpService = new \App\Services\WordpressGameService();
        $countAprovados = 0;
        
        foreach ($jogos as $jogo) {
            $jogo->update(['jgo_status_agendamento' => 'aprovado']);
            
            try {
                $eventType = $jogo->mandante->campeonato->cpo_term_tx_id ?? null;
                $eventCategory = $jogo->mandante->equipe->categoria->cto_term_tx_id ?? null;
                
                $wpPostId = $wpService->sync($jogo, [
                    'event_number' => $jogo->jgo_id,
                    'event_type' => $eventType,
                    'event_category' => $eventCategory,
                ]);

                if ($wpPostId) {
                    $jogo->update(['jgo_wp_id' => $wpPostId]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Erro ao sincronizar jogo aprovado em massa com WP (ID ' . $jogo->jgo_id . '): ' . $e->getMessage());
            }
            $countAprovados++;
        }
        
        return redirect()->back()->with('success', $countAprovados . ' agendamento(s) aprovado(s) com sucesso. Jogos foram sincronizados!');
    }

    // Aprova a data inserida pela comissão
    public function aprovarAgendamento(Request $request, $jogo_id)
    {
        $jogo = Jogo::with(['mandante.campeonato', 'mandante.equipe.categoria'])->findOrFail($jogo_id);
        $jogo->update(['jgo_status_agendamento' => 'aprovado']);

        try {
            $wpService = new \App\Services\WordpressGameService();
            
            $eventType = $jogo->mandante->campeonato->cpo_term_tx_id ?? null;
            $eventCategory = $jogo->mandante->equipe->categoria->cto_term_tx_id ?? null;
            
            $wpPostId = $wpService->sync($jogo, [
                'event_number' => $jogo->jgo_id,
                'event_type' => $eventType,
                'event_category' => $eventCategory,
            ]);

            if ($wpPostId) {
                $jogo->update(['jgo_wp_id' => $wpPostId]);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao sincronizar jogo aprovado com WP: ' . $e->getMessage());
            return redirect()->back()->with('success', 'Agendamento prévio aprovado, mas falhou ao enviar para o WordPress (Log registrado).');
        }

        return redirect()->back()->with('success', 'Agendamento prévio aprovado e jogo marcado oficialmente e sincronizado com o site!');
    }

    // Remove a trava de um jogo pendente
    public function desbloquearAgendamento(Request $request, $jogo_id)
    {
        $jogo = Jogo::findOrFail($jogo_id);
        $jogo->update([
            'jgo_status_agendamento' => 'pendente_preenchimento',
            'jgo_sugerido_por_equipe_id' => null,
            'jgo_dt_jogo' => null,
            'jgo_hora_jogo' => null,
            'jgo_local_jogo_id' => null,
        ]);
        return redirect()->back()->with('success', 'Sugestão cancelada e agendamento desbloqueado.');
    }

    // --- Comissão Técnica Area ---

    // Lista os jogos pendentes de agendamento que envolvem o time da comissão logada
    public function indexComissao(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Administrador');
        $time_id = $user->time_id; // Assume que Comissão Técnica tem time vinculado

        if (!$time_id) {
            $timeResponsavel = $user->timeResponsavel;
            if ($timeResponsavel) {
                $time_id = $timeResponsavel->tim_id;
            }
        }

        if (!$time_id && !$isAdmin) {
            return redirect()->route('dashboard')->withErrors(['error' => 'Usuário não vinculado a um time.']);
        }

        $query = Jogo::with(['mandante.equipe.time', 'visitante.equipe.time', 'mandante.equipe.categoria', 'mandante.campeonato'])
            ->whereIn('jgo_status_agendamento', ['pendente_preenchimento', 'pendente_aprovacao']);

        if (!$isAdmin) {
            $query->where(function ($query) use ($time_id) {
                $query->whereHas('mandante.equipe', function ($q) use ($time_id) {
                    $q->where('eqp_time_id', $time_id);
                })
                ->orWhereHas('visitante.equipe', function ($q) use ($time_id) {
                    $q->where('eqp_time_id', $time_id);
                });
            });
        }

        // Filtro: Categoria
        if ($request->filled('categoria_id')) {
            $catId = $request->categoria_id;
            $query->whereHas('mandante.equipe', function($q) use ($catId) {
                $q->where('eqp_categoria_id', $catId);
            });
        }

        // Filtro: Fase
        if ($request->filled('fase')) {
            $query->where('jgo_fase', 'like', "%{$request->fase}%");
        }

        // Filtro: Status
        if ($request->filled('status')) {
            $query->where('jgo_status_agendamento', $request->status);
        }

        $jogos = $query->paginate(15)->appends($request->all());

        // Also fetch Ginasios for suggesting local
        $ginasios = \App\Models\Ginasio::where('gin_status', true)->get();

        $categorias = \App\Models\Categoria::orderBy('cto_nome')->get();

        return view('agendamentos.comissao.index', compact('jogos', 'ginasios', 'time_id', 'categorias'));
    }

    // Comissão Técnica envia a data/hora para o adversário e Admin
    public function sugerirAgendamento(Request $request, $jogo_id)
    {
        $request->validate([
            'jgo_dt_jogo' => 'required|date',
            'jgo_hora_jogo' => 'required',
            'jgo_local_jogo_id' => 'required|exists:ginasios,gin_id',
        ]);

        $jogo = Jogo::findOrFail($jogo_id);
        
        $user = auth()->user();
        $isAdmin = $user->hasRole('Administrador');
        $time_id = $user->time_id;

        if (!$time_id) {
            $timeResponsavel = $user->timeResponsavel;
            if ($timeResponsavel) {
                $time_id = $timeResponsavel->tim_id;
            }
        }

        if (!$time_id && !$isAdmin) abort(403);

        $jogo->update([
            'jgo_dt_jogo' => $request->jgo_dt_jogo,
            'jgo_hora_jogo' => $request->jgo_hora_jogo,
            'jgo_local_jogo_id' => $request->jgo_local_jogo_id,
            'jgo_status_agendamento' => 'pendente_aprovacao',
            'jgo_sugerido_por_equipe_id' => $time_id, 
        ]);

        return redirect()->back()->with('success', 'Agendamento sugerido. Aguardando aprovação.');
    }

    // Alterna Mandante e Visitante de um jogo pendente
    public function trocarMandante(Request $request, $jogo_id)
    {
        $user = auth()->user();
        $isAdmin = $user->hasRole('Administrador');
        $time_id = $user->time_id;

        if (!$time_id) {
            $timeResponsavel = $user->timeResponsavel;
            if ($timeResponsavel) {
                $time_id = $timeResponsavel->tim_id;
            }
        }

        if (!$time_id && !$isAdmin) abort(403);

        $jogo = Jogo::findOrFail($jogo_id);

        // Only allow swap if status is still 'pendente_preenchimento' (not yet locked/suggested)
        if ($jogo->jgo_status_agendamento !== 'pendente_preenchimento' && !$isAdmin) {
            return redirect()->back()->withErrors(['error' => 'Não é possível alternar o mando de campo após a sugestão de agendamento ser enviada.']);
        }

        // Validate that the logged-in user's team is actually part of this game
        if (!$isAdmin) {
            $equipeIds = \DB::table('equipes')
                ->where('eqp_time_id', $time_id)
                ->join('equipe_campeonato', 'equipes.eqp_id', '=', 'equipe_campeonato.eqp_fk_id')
                ->pluck('equipe_campeonato.eqp_cpo_id');

            $participante = in_array($jogo->jgo_eqp_cpo_mandante_id, $equipeIds->toArray())
                         || in_array($jogo->jgo_eqp_cpo_visitante_id, $equipeIds->toArray());

            if (!$participante) abort(403);
        }

        // Swap
        $oldMandante = $jogo->jgo_eqp_cpo_mandante_id;
        $oldVisitante = $jogo->jgo_eqp_cpo_visitante_id;

        $jogo->update([
            'jgo_eqp_cpo_mandante_id' => $oldVisitante,
            'jgo_eqp_cpo_visitante_id' => $oldMandante,
        ]);

        return redirect()->back()->with('success', 'Mando de campo alternado com sucesso!');
    }
}
