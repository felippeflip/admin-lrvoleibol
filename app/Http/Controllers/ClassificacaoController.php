<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jogo;
use App\Models\Campeonato;
use App\Models\Categoria;
use App\Models\EquipeCampeonato;
use App\Models\Equipe;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;

class ClassificacaoController extends Controller
{
    /**
     * Preview da tabela de classificação antes de publicar.
     */
    public function preview($campeonato_id, $categoria_id)
    {
        $campeonato = Campeonato::findOrFail($campeonato_id);
        $categoria = Categoria::findOrFail($categoria_id);

        $dados = $this->calcularTabela($campeonato_id, $categoria_id);
        
        return view('tabelas.preview', compact('campeonato', 'categoria', 'dados'));
    }

    /**
     * Aprova a tabela e gera o HTML estático.
     */
    public function publicar(Request $request, $campeonato_id, $categoria_id)
    {
        $campeonato = Campeonato::findOrFail($campeonato_id);
        $categoria = Categoria::findOrFail($categoria_id);

        $dados = $this->calcularTabela($campeonato_id, $categoria_id);

        // Renderiza a view Blade "tabela_publica"
        $html = view('tabelas.tabela_publica', compact('campeonato', 'categoria', 'dados'))->render();

        // Nome do arquivo fixo
        $fileName = "tabela_{$campeonato_id}_{$categoria_id}.html";

        // Salva arquivo publicamente no storage
        Storage::disk('public')->put("tabelas/{$fileName}", $html);

        // Marcar todos os jogos "pendentes" como "aprovados" em publicacao (Opcional, mas útil)
        Jogo::whereHas('mandante', function ($q) use ($campeonato_id) {
                $q->where('cpo_fk_id', $campeonato_id);
            })
            ->where(function ($q) use ($categoria_id) {
                $q->whereHas('mandante.equipe', fn($eq) => $eq->where('eqp_categoria_id', $categoria_id));
            })
            ->where('jgo_fase_tipo', 'classificatoria')
            ->update(['jgo_status_publicacao' => 'aprovado']);

        return redirect()->back()->with('success', 'Tabela aprovada, gerada e publicada com sucesso! (ID: ' . $fileName . ')');
    }

    /**
     * Cálculos matemáticos de classificação.
     */
    private function calcularTabela($campeonato_id, $categoria_id)
    {
        // 1. Busca todas as equipes dessa categoria e campeonato
        $equipesCamp = EquipeCampeonato::with('equipe')
            ->where('cpo_fk_id', $campeonato_id)
            ->whereHas('equipe', function ($q) use ($categoria_id) {
                $q->where('eqp_categoria_id', $categoria_id);
            })->get();

        $qtdEquipes = $equipesCamp->count();

        // 2. Busca todos os jogos (classificatória) finalizados com resultado
        $jogos = Jogo::with(['resultadoSets'])
            ->whereHas('mandante', function ($q) use ($campeonato_id) {
                $q->where('cpo_fk_id', $campeonato_id);
            })
            ->whereHas('mandante.equipe', function ($q) use ($categoria_id) {
                $q->where('eqp_categoria_id', $categoria_id);
            })
            ->where('jgo_fase_tipo', 'classificatoria')
            ->whereIn('jgo_res_status', ['pendente', 'aprovado']) // Tem apontamento
            ->get();

        // Também pega jogos de finais pra exibir no rodapé (eliminatórias)
        $jogosFinais = Jogo::with(['mandante.equipe.time', 'visitante.equipe.time', 'resultadoSets'])
            ->whereHas('mandante', function ($q) use ($campeonato_id) {
                $q->where('cpo_fk_id', $campeonato_id);
            })
            ->whereHas('mandante.equipe', function ($q) use ($categoria_id) {
                $q->where('eqp_categoria_id', $categoria_id);
            })
            ->where('jgo_fase_tipo', '!=', 'classificatoria')
            ->orderBy('jgo_dt_jogo')
            ->get();

        // 3. Inicializa tabela
        $tabela = [];
        foreach ($equipesCamp as $eqp) {
            $nomeTime = $eqp->equipe->time->tim_nome ?? 'Time Desconhecido';
            $tabela[$eqp->eqp_cpo_id] = [
                'id' => $eqp->eqp_cpo_id,
                'nome' => $nomeTime,
                'jogos' => 0,
                'vitorias' => 0,
                'derrotas' => 0,
                'pontos' => 0,
                'sets_pro' => 0,
                'sets_con' => 0,
                'pontos_pro' => 0,
                'pontos_con' => 0,
                // Agrupamento default inicial
                'grupo' => 'Único'
            ];
        }

        // Descobrir qual agrupamento usar
        $usaGrupos = false;
        if ($qtdEquipes <= 12) {
            // Turno A e Turno B (O jgo_fase que os define)
            $usaGrupos = true;
        } elseif ($qtdEquipes >= 16) {
            // Grupo A, B, C...
            $usaGrupos = true;
        }

        // Buscar TODOS os jogos da categoria para listagem geral (Realizados e Agendados)
        $todosJogosCategoria = Jogo::with(['mandante.equipe.time', 'visitante.equipe.time', 'resultadoSets', 'ginasio'])
            ->whereHas('mandante', function ($q) use ($campeonato_id) {
                $q->where('cpo_fk_id', $campeonato_id);
            })
            ->whereHas('mandante.equipe', function ($q) use ($categoria_id) {
                $q->where('eqp_categoria_id', $categoria_id);
            })
            ->orderByRaw('ISNULL(jgo_dt_jogo), jgo_dt_jogo ASC')
            ->orderByRaw('ISNULL(jgo_hora_jogo), jgo_hora_jogo ASC')
            ->get();

        if ($usaGrupos) {
            // Mapeia o grupo usando os jogos da classificatória que já estão no $todosJogosCategoria
            $todosJogosClassificatoria = $todosJogosCategoria->where('jgo_fase_tipo', 'classificatoria');

            foreach ($todosJogosClassificatoria as $tj) {
                $mId = $tj->jgo_eqp_cpo_mandante_id;
                $vId = $tj->jgo_eqp_cpo_visitante_id;
                
                $fase = $tj->jgo_fase ?? 'Único';
                $grupoLimpo = $this->extrairGrupo($fase);

                if ($grupoLimpo !== 'Único') {
                    if (isset($tabela[$mId]) && $tabela[$mId]['grupo'] === 'Único') {
                        $tabela[$mId]['grupo'] = $grupoLimpo;
                    }
                    if (isset($tabela[$vId]) && $tabela[$vId]['grupo'] === 'Único') {
                        $tabela[$vId]['grupo'] = $grupoLimpo;
                    }
                }
            }

            // Para as equipes que sobraram como 'Único' e os grupos estão ativados, define como 'A Definir' para ficar mais claro
            foreach ($tabela as $eqpId => $dadosEqp) {
                if ($dadosEqp['grupo'] === 'Único') {
                    $tabela[$eqpId]['grupo'] = 'A Definir';
                }
            }
        }

        // 4. Processa cada jogo com resultados para pontuação
        foreach ($jogos as $j) {
            $mandanteId = $j->jgo_eqp_cpo_mandante_id;
            $visitanteId = $j->jgo_eqp_cpo_visitante_id;

            if (!isset($tabela[$mandanteId]) || !isset($tabela[$visitanteId])) continue;

            $setsGanhosMandante = 0;
            $setsGanhosVisitante = 0;
            $pontosGanhosMandante = 0;
            $pontosGanhosVisitante = 0;

            foreach ($j->resultadoSets as $set) {
                $ptsM = $set->set_pontos_mandante ?? 0;
                $ptsV = $set->set_pontos_visitante ?? 0;

                $pontosGanhosMandante += $ptsM;
                $pontosGanhosVisitante += $ptsV;

                if ($ptsM > $ptsV) {
                    $setsGanhosMandante++;
                } elseif ($ptsV > $ptsM) {
                    $setsGanhosVisitante++;
                }
            }

            // O Jogo foi consolidado
            $tabela[$mandanteId]['jogos']++;
            $tabela[$visitanteId]['jogos']++;

            $tabela[$mandanteId]['sets_pro'] += $setsGanhosMandante;
            $tabela[$mandanteId]['sets_con'] += $setsGanhosVisitante;
            $tabela[$visitanteId]['sets_pro'] += $setsGanhosVisitante;
            $tabela[$visitanteId]['sets_con'] += $setsGanhosMandante;

            $tabela[$mandanteId]['pontos_pro'] += $pontosGanhosMandante;
            $tabela[$mandanteId]['pontos_con'] += $pontosGanhosVisitante;
            $tabela[$visitanteId]['pontos_pro'] += $pontosGanhosVisitante;
            $tabela[$visitanteId]['pontos_con'] += $pontosGanhosMandante;

            // Define Vencedor
            if ($setsGanhosMandante > $setsGanhosVisitante) {
                $tabela[$mandanteId]['vitorias']++;
                $tabela[$visitanteId]['derrotas']++;
                
                // Regra Pontos (3x0, 3x1 => 3 pts) (3x2 => 2 pts winner, 1 loser)
                if ($setsGanhosVisitante == 2) {
                    $tabela[$mandanteId]['pontos'] += 2;
                    $tabela[$visitanteId]['pontos'] += 1;
                } else {
                    $tabela[$mandanteId]['pontos'] += 3;
                }
            } elseif ($setsGanhosVisitante > $setsGanhosMandante) {
                $tabela[$visitanteId]['vitorias']++;
                $tabela[$mandanteId]['derrotas']++;

                if ($setsGanhosMandante == 2) {
                    $tabela[$visitanteId]['pontos'] += 2;
                    $tabela[$mandanteId]['pontos'] += 1;
                } else {
                    $tabela[$visitanteId]['pontos'] += 3;
                }
            }
        }

        // 5. Agrupa e Ordena
        // Separar por grupos
        $grupos = [];
        foreach ($tabela as $eqpStats) {
            $g = $usaGrupos ? $eqpStats['grupo'] : 'Fase Única';
            if (!isset($grupos[$g])) {
                $grupos[$g] = [];
            }
            // Calcula Averages
            $eqpStats['set_avg'] = $eqpStats['sets_con'] > 0 ? ($eqpStats['sets_pro'] / $eqpStats['sets_con']) : ($eqpStats['sets_pro'] > 0 ? 999 : 0);
            $eqpStats['pt_avg'] = $eqpStats['pontos_con'] > 0 ? ($eqpStats['pontos_pro'] / $eqpStats['pontos_con']) : ($eqpStats['pontos_pro'] > 0 ? 999 : 0);

            $grupos[$g][] = $eqpStats;
        }

        // Ordenar cada grupo: Pontos DESC -> Set Avg DESC -> Ponto Avg DESC
        foreach ($grupos as $k => $gList) {
            usort($gList, function($a, $b) {
                if ($a['pontos'] != $b['pontos']) return $b['pontos'] <=> $a['pontos'];
                if ($a['set_avg'] != $b['set_avg']) return $b['set_avg'] <=> $a['set_avg'];
                if ($a['pt_avg'] != $b['pt_avg']) return $b['pt_avg'] <=> $a['pt_avg'];
                return 0; // Se tudo for igual, ID (pode adicionar confronto direto aqui se houver)
            });
            $grupos[$k] = $gList;
        }
        
        // Sort keys (Group A, Group B...)
        ksort($grupos);

        return [
            'grupos' => $grupos,
            'finais' => $jogosFinais,
            'equipes' => $equipesCamp,
            'jogos_todos' => $todosJogosCategoria
        ];
    }

    private function extrairGrupo($faseString)
    {
        // Padrões: "Grupo A", "Turno A", "Grupo B - Turno Único", etc.
        // Se houver "Grupo X", assume Grupo X.
        if (preg_match('/(Grupo\s[A-Z0-9]+)/i', $faseString, $matches)) {
            return $matches[1];
        }
        // Se houver "Turno X", assume Turno X (pra 12 ou menos equipes)
        if (preg_match('/(Turno\s[A-Z0-9]+)/i', $faseString, $matches)) {
            return $matches[1];
        }
        return 'Único';
    }
}
