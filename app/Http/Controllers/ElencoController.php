<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipeCampeonato;
use App\Models\Atleta;
use App\Models\ElencoEquipeCampeonato;

class ElencoController extends Controller
{
    
    public function list()
    {
        $user = auth()->user();
        
        $participacoes = EquipeCampeonato::with(['equipe.time', 'campeonato'])
            ->when(!$user->hasRole('Administrador'), function($query) use ($user) {
                // Se NÃO for Administrador, filtra pelas equipes do usuário
                return $query->whereHas('equipe.time', function($q) use ($user) {
                    $q->where('tim_user_id', $user->id);
                });
            })
            // Exibir apenas campeonatos ATIVOS para gestão
            ->whereHas('campeonato', function($q) {
                $q->where('cpo_ativo', true);
            })
            ->orderBy('created_at', 'desc')

            ->paginate(15);

        return view('campeonatos.elenco.list', compact('participacoes'));
    }

    public function index($campeonatoId, $equipeCampeonatoId)
    {
        $equipeCampeonato = EquipeCampeonato::with(['equipe.time', 'campeonato', 'elenco.atleta'])
            ->findOrFail($equipeCampeonatoId);

        // Verificação de Autorização (Isolamento)
        $user = auth()->user();
        if (!$user->hasRole('Administrador') && $equipeCampeonato->equipe->time->tim_user_id !== $user->id) {
            abort(403, 'Acesso não autorizado a esta equipe.');
        }

        $timeId = $equipeCampeonato->equipe->time->tim_id; // ID do Time dono da Equipe

        // Buscar atletas DISPONÍVEIS do time (que ainda não estão neste elenco deste campeonato)
        // IDs dos atletas já no elenco
        $atletasNoElencoIds = [];
        if ($equipeCampeonato->elenco) {
            foreach($equipeCampeonato->elenco as $elencoItem) {
                // O relacionamento 'elenco' retorna ElencoEquipeCampeonato (pivot com dados extras)
                // mas precisamos acessar o ID do atleta.
                // Ajuste: A relação 'elenco' no model EquipeCampeonato não foi mostrada,
                // mas baseada na convenção N:N e no model Atleta:
                // Atleta::participacoes() -> belongsToMany(EquipeCampeonato)
                // Então EquipeCampeonato::atletas() (ou similar) deveria existir.
                // Vou assumir que vamos pegar via a tabela pivot ou criar a relação se não existir.
                
                // Vamos usar a query direta na pivot para garantir
                $atletasNoElencoIds[] = $elencoItem->ele_fk_atl_id;
            }
        }
        
        // Melhor abordagem: Usar a relação definida se houver, ou a tabela pivot.
        // No model Atleta temos 'participacoes'. 
        // No model EquipeCampeonato ainda não vi a relação 'atletas' ou 'elenco' (vi 'equipe' e 'campeonato').
        // Vou precisar verificar/adicionar a relação no model EquipeCampeonato ou usar query direta.
        
        // Assumindo Query direta para ser mais seguro por enquanto:
        $atletasNoElencoIds = ElencoEquipeCampeonato::where('ele_fk_eqp_cpo_id', $equipeCampeonatoId)
                                ->pluck('ele_fk_atl_id')
                                ->toArray();

        $categoriaId = $equipeCampeonato->equipe->eqp_categoria_id; // ID da Categoria da Equipe
        
        $atletasDisponiveis = Atleta::where('atl_tim_id', $timeId)
            ->where('atl_categoria', $categoriaId) // Filtrar apenas atletas desta categoria
            ->whereNotIn('atl_id', $atletasNoElencoIds)
            ->where('atl_ativo', 1) // Opcional: apenas ativos
            ->orderBy('atl_nome')
            ->get();
            
        // Carregar os dados do elenco completos (com nome do atleta, camisa, posição)
        $elencoAtual = ElencoEquipeCampeonato::with('atleta')
                        ->where('ele_fk_eqp_cpo_id', $equipeCampeonatoId)
                        ->get();

        return view('campeonatos.elenco.index', compact('equipeCampeonato', 'atletasDisponiveis', 'elencoAtual'));
    }

    public function store(Request $request, $campeonatoId, $equipeCampeonatoId)
    {
        $request->validate([
            'atleta_id' => 'required|exists:atletas,atl_id',
            'numero_camisa' => 'required|integer',
            'posicao' => 'nullable|string|max:15',
        ]);

        $equipeCampeonato = EquipeCampeonato::with('equipe.time')->findOrFail($equipeCampeonatoId);

        // Verificação de Autorização (Novamente para garantir segurança no POST)
        $user = auth()->user();
        if (!$user->hasRole('Administrador') && $equipeCampeonato->equipe->time->tim_user_id !== $user->id) {
            abort(403, 'Ação não autorizada.');
        }

        // Validação Adicional: O atleta pertence mesmo ao time desta equipe?
        $atleta = Atleta::findOrFail($request->atleta_id);
        
        // Cuidado: $equipeCampeonato->equipe->time é o objeto time.
        if ($atleta->atl_tim_id !== $equipeCampeonato->equipe->time->tim_id) {
             return back()->with('error', 'Este atleta não pertence ao time desta equipe.');
        }

        // Verificar unicidade
        $exists = ElencoEquipeCampeonato::where('ele_fk_eqp_cpo_id', $equipeCampeonatoId)
            ->where('ele_fk_atl_id', $request->atleta_id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'Atleta já está no elenco deste campeonato.');
        }

        ElencoEquipeCampeonato::create([
            'ele_fk_eqp_cpo_id' => $equipeCampeonatoId,
            'ele_fk_atl_id' => $request->atleta_id,
            'ele_num_camisa' => $request->numero_camisa,
            'ele_posicao_atuando' => $request->posicao,
        ]);

        return back()->with('success', 'Atleta adicionado ao elenco com sucesso!');
    }

    public function destroy($campeonatoId, $equipeCampeonatoId, $elencoId)
    {
        $elenco = ElencoEquipeCampeonato::findOrFail($elencoId);
        
        // Carregar relacionamento para verificar permissão
        // Note: ele_fk_eqp_cpo_id relates to EquipeCampeonato
        $eqpCpo = EquipeCampeonato::with('equipe.time')->findOrFail($elenco->ele_fk_eqp_cpo_id);

        $user = auth()->user();
        if (!$user->hasRole('Administrador') && $eqpCpo->equipe->time->tim_user_id !== $user->id) {
             abort(403, 'Ação não autorizada.');
        }

        $elenco->delete();

        return back()->with('success', 'Atleta removido do elenco.');
    }
}
