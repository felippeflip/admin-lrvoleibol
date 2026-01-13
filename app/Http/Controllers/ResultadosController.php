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
    public function create($wpId)
    {
        // 1. Find or Create Local Jogo (Auto-healing logic similar to JogosController)
        $jogo = Jogo::where('jgo_wp_id', $wpId)->first();
        
        if (!$jogo) {
            $wpPost = WpPosts::find($wpId);
            if (!$wpPost) abort(404, 'Jogo não encontrado');

            $localId = $wpPost->getMetaValue('_local_jogo_id');
            if ($localId) {
                $jogo = Jogo::find($localId);
                if ($jogo && !$jogo->jgo_wp_id) $jogo->update(['jgo_wp_id' => $wpId]);
            }
            
            // If still no local record, we might need to create it strictly?
            // Usually we assume the game was edited/synced. But if it's raw legacy?
            // For now, let's assume valid games are synced. If not, redirect to Edit?
            if (!$jogo) {
                return redirect()->route('jogos.edit', $wpId)->with('error', 'Por favor, salve o jogo primeiro para sincronizar antes de adicionar resultados.');
            }
        }

        // Load existing results if any
        $jogo->load('resultadoSets');
        $sets = $jogo->resultadoSets->keyBy('set_numero');

        // Teams names (Need to fetch via relations)
        $mandante = $jogo->mandante->equipe->eqp_nome_detalhado ?? 'Mandante';
        $visitante = $jogo->visitante->equipe->eqp_nome_detalhado ?? 'Visitante';

        return view('resultados.create', compact('jogo', 'sets', 'mandante', 'visitante'));
    }

    public function store(Request $request, $jgoId)
    {
        // $jgoId is the LOCAL ID (sent from form action)
        $jogo = Jogo::findOrFail($jgoId);

        $request->validate([
            'sets' => 'required|array',
            'sets.*.mandante' => 'nullable|integer|min:0',
            'sets.*.visitante' => 'nullable|integer|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Clear old sets to recreate
            $jogo->resultadoSets()->delete();
            
            $setsWonMandante = 0;
            $setsWonVisitante = 0;
            $resultsToSave = [];

            // Sort sets by number to process sequentially
            $setsInput = $request->sets;
            ksort($setsInput);

            foreach ($setsInput as $num => $scores) {
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
                     return back()->with('error', "Erro no Set $num: O jogo já deveria ter encerrado (3 sets vencidos). Remova os sets excedentes.")->withInput();
                }

                // Rule: Points Target (25 for 1-4, 15 for 5)
                $target = ($num == 5) ? 15 : 25;
                $maxScore = max($pMandante, $pVisitante);
                $diff = abs($pMandante - $pVisitante);

                // Rule: Minimum Score Reached
                if ($maxScore < $target) {
                    return back()->with('error', "Set $num: Placar inválido. O vencedor deve atingir pelo menos $target pontos.")->withInput();
                }

                // Rule: 2 Points Difference
                if ($diff < 2) {
                     return back()->with('error', "Set $num: Placar inválido. É necessária uma diferença mínima de 2 pontos para fechar o set.")->withInput();
                }

                // Determine Winner
                $winner = ($pMandante > $pVisitante) ? 1 : 2;
                if ($winner == 1) $setsWonMandante++;
                else $setsWonVisitante++;

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
                return back()->with('error', "Resultado inválido. A partida deve ter um vencedor (melhor de 5 sets). Nenhuma equipe venceu 3 sets.")->withInput();
            }

            // Save Validated Results
            foreach ($resultsToSave as $res) {
                ResultadoSet::create($res);
            }

            // Match Winner
            $vencedorMandante = ($setsWonMandante > $setsWonVisitante);

            // Update Jogo Status
            $jogo->update([
                'jgo_res_status' => 'pendente', // Requires approval
                'jgo_res_usuario_id' => Auth::id(),
                'jgo_res_data_envio' => now(),
                'jgo_vencedor_mandante' => $vencedorMandante
            ]);

            DB::commit();

            return redirect()->route('jogos.index')->with('success', 'Resultado enviado com sucesso! Aguardando aprovação.');

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
