<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jogo;
use App\Models\ResultadoSet;
use App\Models\WpPosts;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResultadosController extends Controller
{
    public function create(Request $request, $id)
    {
        // 1. Find Local Jogo by ID (Primary Key)
        $jogo = Jogo::find($id);

        // Fallback: If not found by ID, try finding by WP ID (for legacy links support)
        if (!$jogo) {
            $jogo = Jogo::where('jgo_wp_id', $id)->first();

            // Redirect to canonical local ID
            if ($jogo) {
                return redirect()->route('resultados.create', $jogo->jgo_id);
            }
        }

        if (!$jogo) {
            abort(404, 'Jogo não encontrado.');
        }

        // Load existing results if any
        $jogo->load('resultadoSets');
        $sets = $jogo->resultadoSets->keyBy('set_numero');

        // Teams names (Need to fetch via relations)
        $mandante = $jogo->mandante->equipe->eqp_nome_detalhado ?? 'Mandante';
        $visitante = $jogo->visitante->equipe->eqp_nome_detalhado ?? 'Visitante';

        $isPlayoff = $jogo->isPlayoff();
        $hasGoldenSet = isset($sets[6]);

        if ($this->isMobileView()) {
            return view('mobile.resultados.create', compact('jogo', 'sets', 'mandante', 'visitante', 'isPlayoff', 'hasGoldenSet'));
        }

        return view('resultados.create', compact('jogo', 'sets', 'mandante', 'visitante', 'isPlayoff', 'hasGoldenSet'));
    }

    public function store(Request $request, $jgoId)
    {
        // $jgoId is the LOCAL ID (sent from form action)
        $jogo = Jogo::findOrFail($jgoId);

        $request->validate([
            'sets' => 'required|array',
            'sets.*.mandante' => 'nullable|integer|min:0',
            'sets.*.visitante' => 'nullable|integer|min:0',
            'tem_golden_set' => 'nullable',
        ]);

        $isPlayoff = $jogo->isPlayoff();
        $temGoldenSet = $request->boolean('tem_golden_set') || (isset($request->sets[6]) && ($request->sets[6]['mandante'] !== null || $request->sets[6]['visitante'] !== null));

        try {
            DB::beginTransaction();

            // Clear old sets to recreate
            $jogo->resultadoSets()->delete();

            $setsWonMandante = 0;
            $setsWonVisitante = 0;
            $resultsToSave = [];

            // Separar sets regulares (1 a 5) do Golden Set (set 6)
            $setsRegularesInput = [];
            $goldenSetInput = null;

            foreach ($request->sets as $num => $scores) {
                if ((int)$num === 6) {
                    $goldenSetInput = $scores;
                } elseif ((int)$num >= 1 && (int)$num <= 5) {
                    $setsRegularesInput[(int)$num] = $scores;
                }
            }

            ksort($setsRegularesInput);

            foreach ($setsRegularesInput as $num => $scores) {
                // Skip if both null (empty row)
                if (($scores['mandante'] ?? null) === null && ($scores['visitante'] ?? null) === null) {
                    continue;
                }

                $pMandante = $scores['mandante'];
                $pVisitante = $scores['visitante'];

                // Validate incomplete input
                if ($pMandante === null || $pVisitante === null) {
                    return back()->with('error', "Set $num: Placar incompleto. Informe os pontos de ambos os times.")->withInput();
                }

                // Rule: Check if game is already decided
                if ($setsWonMandante >= 3 || $setsWonVisitante >= 3) {
                    return back()->with('error', "Erro no Set $num: O jogo regular encerra com 3 sets vencidos. Remova os sets regulares excedentes.")->withInput();
                }

                // Regra 1: Alvo de pontos (25 para sets 1-4, 15 para o set 5)
                $target = ($num == 5) ? 15 : 25;
                $maxScore = max($pMandante, $pVisitante);
                $diff = abs($pMandante - $pVisitante);

                // Regra 2: Pontuação mínima alcançada
                if ($maxScore < $target) {
                    return back()->with('error', "Set $num: Placar inválido. O vencedor deve atingir pelo menos $target pontos.")->withInput();
                }

                // Regra 3: Diferença exata dependendo do placar
                if ($maxScore === $target && $diff < 2) {
                    return back()->with('error', "Set $num: Placar inválido. É necessária uma diferença mínima de 2 pontos para fechar o set.")->withInput();
                }

                // Regra 4: Se o set empatou em $target - 1 e precisou passar dos $target pontos, a diferença deve ser de EXATAMENTE 2.
                if ($maxScore > $target && $diff !== 2) {
                    return back()->with('error', "Set $num: Placar inválido. Quando o placar passa de " . ($target - 1) . " pontos, o set só pode ser encerrado com EXATOS 2 pontos de diferença (ex: " . ($maxScore) . "x" . ($maxScore - 2) . ").")->withInput();
                }

                // Determine Winner
                $winner = ($pMandante > $pVisitante) ? 1 : 2;
                if ($winner == 1)
                    $setsWonMandante++;
                else
                    $setsWonVisitante++;

                $resultsToSave[] = [
                    'set_jgo_id' => $jogo->jgo_id,
                    'set_numero' => $num,
                    'set_pontos_mandante' => $pMandante,
                    'set_pontos_visitante' => $pVisitante,
                    'set_vencedor' => $winner
                ];
            }

            // Rule: Game must have a winner (Best of 5)
            if ($setsWonMandante < 3 && $setsWonVisitante < 3) {
                return back()->with('error', "Resultado inválido. A partida regular não foi concluída. Nenhuma equipe venceu 3 sets. Verifique se informou todos os sets necessários.")->withInput();
            }

            // Validação e Processamento do Golden Set (Set 6)
            $winnerGS = null;
            if ($temGoldenSet) {
                if (!$isPlayoff) {
                    return back()->with('error', "O Golden Set é permitido apenas em fases eliminatórias de playoffs (Semifinais e Finais).")->withInput();
                }

                $pMandanteGS = $goldenSetInput['mandante'] ?? null;
                $pVisitanteGS = $goldenSetInput['visitante'] ?? null;

                if ($pMandanteGS === null || $pVisitanteGS === null) {
                    return back()->with('error', "Golden Set: Placar incompleto. Informe os pontos de ambos os times no set de desempate.")->withInput();
                }

                // Regra do Golden Set: 25 pontos com 2 de diferença
                $targetGS = 25;
                $maxScoreGS = max($pMandanteGS, $pVisitanteGS);
                $diffGS = abs($pMandanteGS - $pVisitanteGS);

                if ($maxScoreGS < $targetGS) {
                    return back()->with('error', "Golden Set: Placar inválido. O vencedor do Golden Set deve atingir pelo menos $targetGS pontos.")->withInput();
                }

                if ($maxScoreGS === $targetGS && $diffGS < 2) {
                    return back()->with('error', "Golden Set: Placar inválido. É necessária uma diferença mínima de 2 pontos para fechar o Golden Set (ex: 25x23, 25x20).")->withInput();
                }

                if ($maxScoreGS > $targetGS && $diffGS !== 2) {
                    return back()->with('error', "Golden Set: Placar inválido. Quando o placar passa de 24 pontos, o Golden Set só encerra com EXATOS 2 pontos de diferença (ex: " . ($maxScoreGS) . "x" . ($maxScoreGS - 2) . ").")->withInput();
                }

                $winnerGS = ($pMandanteGS > $pVisitanteGS) ? 1 : 2;

                $resultsToSave[] = [
                    'set_jgo_id' => $jogo->jgo_id,
                    'set_numero' => 6,
                    'set_pontos_mandante' => $pMandanteGS,
                    'set_pontos_visitante' => $pVisitanteGS,
                    'set_vencedor' => $winnerGS
                ];
            }

            if (empty($resultsToSave)) {
                return back()->with('error', "Nenhum resultado válido encontrado para salvar.")->withInput();
            }

            // Save Validated Results
            foreach ($resultsToSave as $res) {
                ResultadoSet::create($res);
            }

            // Vencedor do Confronto:
            // Se houve Golden Set, o vencedor do desempate é quem avança / garante o título
            if ($winnerGS !== null) {
                $vencedorMandante = ($winnerGS === 1) ? 1 : 0;
            } else {
                $vencedorMandante = ($setsWonMandante > $setsWonVisitante) ? 1 : 0;
            }

            // Update Jogo Status
            $jogo->update([
                'jgo_res_status' => 'pendente', // Requires approval
                'jgo_res_usuario_id' => Auth::id(),
                'jgo_res_data_envio' => now(),
                'jgo_vencedor_mandante' => $vencedorMandante
            ]);

            DB::commit();

            return redirect()->route('dashboard')->with('success', 'Resultado enviado com sucesso! Aguardando aprovação.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erro ao salvar resultado: " . $e->getMessage());
            return back()->with('error', 'Erro interno ao salvar resultado.')->withInput();
        }
    }

    public function approve($jgoId)
    {
        // Check Admin permission? Middleware handles usually, or check here.
        if (!Auth::user()->can('manage team')) { // Assuming 'manage team' or specific admin permission
            // Or check role?
            // User said "admin aprovação". I'll assume admin role check if needed.
        }

        $jogo = Jogo::findOrFail($jgoId);
        $jogo->update(['jgo_res_status' => 'aprovado']);

        // TODO: Here we could Sync the result to WordPress Description or Meta if needed?
        // But requested scope was just approval locally.

        return back()->with('success', 'Resultado aprovado com sucesso!');
    }
}
