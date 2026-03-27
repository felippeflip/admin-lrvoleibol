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
            $query->where('eqp_nome_detalhado', 'like', '%' . $request->search_equipe . '%');
        }

        // Filtro por Time (Select)
        if ($request->filled('search_time')) {
            $query->whereHas('time', function ($q) use ($request) {
                $q->where('tim_id', $request->search_time);
            });
        }

        // Filtro por Categoria (Select)
        if ($request->filled('search_categoria')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('cto_id', $request->search_categoria);
            });
        }

        // Filtro por Nome do Treinador
        if ($request->filled('search_treinador')) {
            $query->where('eqp_nome_treinador', 'like', '%' . $request->search_treinador . '%');
        }

        $equipes = $query->paginate(10)->appends($request->all());

        // Carregar opções para os selects de filtro
        $times = Time::where('tim_status', 1)->orderBy('tim_nome')->get();
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

        $categorias = Categoria::orderBy('cto_nome')->get();

        // Passa as duas listas de equipes para a view
        return view('equipes_campeonato.create', compact('campeonato', 'equipesDisponiveis', 'equipesInscritas', 'categorias'));
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

            // Identifica as equipes que serão removidas
            $equipesAtuaisIds = $campeonato->equipes()->pluck('equipes.eqp_id')->toArray();
            $equipesMantidasIds = array_keys($dataToSync);
            $equipesParaRemover = array_diff($equipesAtuaisIds, $equipesMantidasIds);

            $msgAdicional = '';

            if (!empty($equipesParaRemover)) {
                $wpService = new \App\Services\WordpressGameService();
                $totalAnulados = 0;
                $totalDeletados = 0;

                foreach ($equipesParaRemover as $eqpParaRemoverId) {
                    $equipeCampeonato = \App\Models\EquipeCampeonato::where('cpo_fk_id', $campeonato->cpo_id)
                        ->where('eqp_fk_id', $eqpParaRemoverId)
                        ->first();

                    if (!$equipeCampeonato) continue;

                    // Verifica se a equipe possui elenco inscrito
                    if ($equipeCampeonato->elenco()->exists()) {
                        return redirect()->back()->withErrors(['error' => "A equipe " . ($equipeCampeonato->equipe->eqp_nome_detalhado ?? 'desconhecida') . " possui elenco cadastrado neste campeonato e não pode ser removida. Remova o elenco antes."]);
                    }

                    // Processa os jogos desta equipe sendo removida
                    $jogos = \App\Models\Jogo::where('jgo_eqp_cpo_mandante_id', $equipeCampeonato->eqp_cpo_id)
                        ->orWhere('jgo_eqp_cpo_visitante_id', $equipeCampeonato->eqp_cpo_id)
                        ->get();

                    $anuladosDestaEquipe = 0;

                    foreach ($jogos as $jogo) {
                        $isRealizado = $jogo->jgo_status_agendamento === 'realizado' 
                                    || $jogo->jgo_resultado_aprovado == 1 
                                    || in_array($jogo->jgo_res_status, ['pendente', 'aprovado']);

                        if ($isRealizado) {
                            $jogo->update([
                                'jgo_status_agendamento' => 'anulado',
                                'jgo_res_status' => 'anulado'
                            ]);

                            if ($jogo->jgo_wp_id) {
                                $wpService->delete($jogo->jgo_wp_id);
                            }
                            $anuladosDestaEquipe++;
                            $totalAnulados++;
                        } else {
                            if ($jogo->jgo_wp_id) {
                                $wpService->delete($jogo->jgo_wp_id);
                            }
                            $jogo->delete();
                            $totalDeletados++;
                        }
                    }

                    // Se a equipe tinha jogos anulados, não podemos desvincular do campeonato
                    // pois precisamos preservar o histórico dos confrontos passados!
                    if ($anuladosDestaEquipe > 0) {
                        // Re-adicionamos aos dados para sync para que não seja detached
                        $dataToSync[$eqpParaRemoverId] = [
                            'eqp_cpo_dt_inscricao' => $equipeCampeonato->eqp_cpo_dt_inscricao
                        ];
                    }
                }

                if ($totalAnulados > 0 || $totalDeletados > 0) {
                    $msgAdicional = " Ao processar as remoções: $totalDeletados jogo(s) futuro(s) excluído(s) e $totalAnulados jogo(s) realizado(s) anulado(s). As equipes com jogos realizados foram mantidas ativas no sistema apenas para preservar o histórico.";
                }
            }

            // Usa sync para remover as equipes que não foram selecionadas e adicionar as novas
            $campeonato->equipes()->sync($dataToSync);

            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)->with('success', 'Equipes atualizadas no campeonato com sucesso!' . $msgAdicional);

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
            // Verifica se a equipe possui elenco cadastrado no campeonato
            $possuiElenco = \App\Models\EquipeCampeonato::where('cpo_fk_id', $campeonato->cpo_id)
                ->where('eqp_fk_id', $equipe->eqp_id)
                ->whereHas('elenco') // Verifica relacionamento com elenco
                ->exists();

            if ($possuiElenco) {
                return redirect()->back()->withErrors(['error' => 'Esta equipe já possui elenco cadastrado neste campeonato e não pode ser removida antes de remover seu elenco.']);
            }

            $equipeCampeonato = \App\Models\EquipeCampeonato::where('cpo_fk_id', $campeonato->cpo_id)
                ->where('eqp_fk_id', $equipe->eqp_id)
                ->first();

            if (!$equipeCampeonato) {
                return redirect()->back()->withErrors(['error' => 'A equipe não está vinculada a este campeonato.']);
            }

            // Busca os jogos da equipe neste campeonato
            $jogos = \App\Models\Jogo::where('jgo_eqp_cpo_mandante_id', $equipeCampeonato->eqp_cpo_id)
                ->orWhere('jgo_eqp_cpo_visitante_id', $equipeCampeonato->eqp_cpo_id)
                ->get();

            $wpService = new \App\Services\WordpressGameService();
            $deletados = 0;
            $anulados = 0;

            foreach ($jogos as $jogo) {
                $isRealizado = $jogo->jgo_status_agendamento === 'realizado' 
                            || $jogo->jgo_resultado_aprovado == 1 
                            || in_array($jogo->jgo_res_status, ['pendente', 'aprovado']);

                if ($isRealizado) {
                    // Anular do campeonato local
                    $jogo->update([
                        'jgo_status_agendamento' => 'anulado',
                        'jgo_res_status' => 'anulado'
                    ]);

                    // Deletar do WP para não aparecer publicamente o jogo com pontuação
                    if ($jogo->jgo_wp_id) {
                        $wpService->delete($jogo->jgo_wp_id);
                    }
                    $anulados++;
                } else {
                    // Jogo futuro / não preenchido -> Deletar
                    if ($jogo->jgo_wp_id) {
                        $wpService->delete($jogo->jgo_wp_id);
                    }
                    $jogo->delete();
                    $deletados++;
                }
            }

            // Se não restou ou havia jogos realizados, podemos prosseguir com o delete do pivot (remove equipe do campeonato)
            if ($anulados === 0) {
                $campeonato->equipes()->detach($equipe->eqp_id);
                $msg = "Equipe removida do campeonato com sucesso! $deletados jogo(s) futuro(s) excluído(s).";
            } else {
                $msg = "A equipe possuía $anulados jogo(s) já realizado(s), os quais foram anulados para não computar pontos na tabela. Além disso, $deletados jogo(s) futuro(s) foram excluídos. A equipe foi mantida no registro do campeonato para preservar o histórico dos confrontos anulados.";
            }

            return redirect()->route('equipes.campeonato.index', $campeonato->cpo_id)->with('success', $msg);

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Erro ao remover equipe do campeonato: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->withErrors(['error' => 'Erro ao processar a remoção ou anulação dos jogos associados à equipe.']);
        }
    }
    /**
     * Retorna a lista de equipes inscritas em um campeonato em formato JSON.
     * Usado no formulário de criação de jogos.
     */
    public function listByCampeonatoJson($campeonatoId)
    {
        $equipes = \App\Models\EquipeCampeonato::where('cpo_fk_id', $campeonatoId)
            ->with(['equipe.time', 'equipe.categoria'])
            ->get()
            ->map(function ($item) {
                $timeNome = $item->equipe->time->tim_nome ?? 'Sem Time';
                $categoriaNome = $item->equipe->categoria->cto_nome ?? 'Sem Categoria';
                $nomeFormatado = "{$timeNome} - {$categoriaNome}";

                return [
                    'id' => $item->eqp_cpo_id, // ID da tabela pivot
                    'nome' => $nomeFormatado
                ];
            });

        return response()->json($equipes);
    }
}
