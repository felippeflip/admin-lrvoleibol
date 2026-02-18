<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EquipeCampeonato;
use App\Models\Atleta;
use App\Models\ElencoEquipeCampeonato;
use App\Models\Campeonato; // Import do model Campeonato
use App\Models\Categoria; // Import do model Categoria
use App\Models\Equipe; // Import do model Equipe

class ElencoController extends Controller
{

    public function list(Request $request)
    {
        $user = auth()->user();

        $participacoes = EquipeCampeonato::with(['equipe.time', 'campeonato'])
            ->when(!$user->hasRole('Administrador'), function ($query) use ($user) {
                // Se NÃO for Administrador, filtra pelas equipes do usuário
                return $query->whereHas('equipe.time', function ($q) use ($user) {
                    if ($user->hasRole('ResponsavelTime')) {
                        $q->where('tim_user_id', $user->id);
                    } elseif ($user->hasRole('ComissaoTecnica')) {
                        $q->where('tim_id', $user->time_id);
                    } else {
                        $q->where('tim_id', 0); // Deny others
                    }
                });
            })
            // Exibir apenas campeonatos ATIVOS para gestão
            ->whereHas('campeonato', function ($q) {
                $q->where('cpo_ativo', true);
            })
            // Filtros da Pesquisa
            ->when($request->filled('campeonato_id'), function ($q) use ($request) {
                $q->where('cpo_fk_id', $request->campeonato_id);
            })
            ->when($request->filled('equipe_id'), function ($q) use ($request) {
                // Filtra pelo id da equipe (eqp_id) que está na relação 'equipe'
                $q->whereHas('equipe', function ($qEqp) use ($request) {
                    $qEqp->where('eqp_id', $request->equipe_id);
                });
            })
            ->when($request->filled('categoria_id'), function ($q) use ($request) {
                $q->whereHas('equipe', function ($qEqp) use ($request) {
                    $qEqp->where('eqp_categoria_id', $request->categoria_id);
                });
            })

            ->orderBy('created_at', 'desc')

            ->paginate(15)
            ->appends($request->all());

        // Carregar listas para os dropdowns
        $campeonatos = Campeonato::where('cpo_ativo', true)->orderBy('cpo_nome')->get();
        $categorias = Categoria::orderBy('cto_nome')->get();

        // Equipes: se for admin carrega todas, se não, carrega só as do usuário
        if ($user->hasRole('Administrador')) {
            $equipes = Equipe::orderBy('eqp_nome_detalhado')->get();
        } else {
            $equipes = Equipe::whereHas('time', function ($q) use ($user) {
                if ($user->hasRole('ResponsavelTime')) {
                    $q->where('tim_user_id', $user->id);
                } elseif ($user->hasRole('ComissaoTecnica')) {
                    $q->where('tim_id', $user->time_id);
                }
            })->orderBy('eqp_nome_detalhado')->get();
        }

        return view('campeonatos.elenco.list', compact('participacoes', 'campeonatos', 'categorias', 'equipes'));
    }

    public function index($campeonatoId, $equipeCampeonatoId)
    {
        $equipeCampeonato = EquipeCampeonato::with(['equipe.time', 'campeonato', 'elenco.atleta'])
            ->findOrFail($equipeCampeonatoId);

        // Verificação de Autorização (Isolamento)
        // Verificação de Autorização (Isolamento)
        $user = auth()->user();
        if (!$user->hasRole('Administrador')) {
            $allowed = false;
            // Check Responsavel
            if ($user->hasRole('ResponsavelTime') && $equipeCampeonato->equipe->time->tim_user_id === $user->id) {
                $allowed = true;
            }
            // Check Comissao
            if ($user->hasRole('ComissaoTecnica') && $equipeCampeonato->equipe->time->tim_id === $user->time_id) {
                $allowed = true;
            }
            
            if (!$allowed) {
                abort(403, 'Acesso não autorizado a esta equipe.');
            }
        }

        $timeId = $equipeCampeonato->equipe->time->tim_id; // ID do Time dono da Equipe

        // Buscar atletas DISPONÍVEIS do time (que ainda não estão neste elenco deste campeonato)
        // IDs dos atletas já no elenco
        $atletasNoElencoIds = [];
        if ($equipeCampeonato->elenco) {
            foreach ($equipeCampeonato->elenco as $elencoItem) {
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

        // ID da Categoria da Equipe
        $categoriaId = $equipeCampeonato->equipe->eqp_categoria_id;

        // Dados da Categoria da Equipe (para pegar idade_maxima)
        // Assume-se que a relação 'categoria' já foi carregada ou pode ser acessada.
        // Se precisar carregar: $equipeCampeonato->equipe->load('categoria');
        $categoriaEquipe = $equipeCampeonato->equipe->categoria;
        $maxAge = $categoriaEquipe ? $categoriaEquipe->cto_idade_maxima : null;
        $anoCampeonato = $equipeCampeonato->campeonato->cpo_ano ?? date('Y'); // Fallback para ano atual

        $atletasDisponiveis = Atleta::with('categoria')
            ->where('atl_tim_id', $timeId)
            ->whereNotIn('atl_id', $atletasNoElencoIds)
            ->where('atl_ativo', 1) // Opcional: apenas ativos

            // Lógica de Idade / Categoria
            // 1. Carrega todos os candidatos (filtro leve de banco)
            // 2. Filtra a idade exata no PHP para precisão (considerando meses e dias)
            ->get()
            ->filter(function ($atleta) use ($categoriaId, $maxAge) {
                if (is_null($maxAge)) {
                    return true; // Categoria Livre
                }

                // Idade exata hoje (Carbon considera dia e mês)
                // Ex: Nascido em 16/09/2011. Hoje 12/02/2026.
                // 2026 - 2011 = 15 anos (pelo ano).
                // Mas ainda não fez aniversário, então tem 14 anos.
    
                $idadeReal = \Carbon\Carbon::parse($atleta->atl_dt_nasc)->age;

                // Regra: Atleta deve ter idade <= maxAge.
                // Atletas mais novos podem jogar em categorias mais velhas.
                return $idadeReal <= $maxAge;
            });

        // O filter retorna uma Collection com chaves preservadas, o que pode ser estranho para o frontend se esperar array indexado sequencialmente.
        // Vamos usar values() para reindexar.
        $atletasDisponiveis = $atletasDisponiveis->values();

        // Carregar os dados do elenco completos (com nome do atleta, camisa, posição)
        $elencoAtual = ElencoEquipeCampeonato::with('atleta.categoria')
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
        // Verificação de Autorização (Novamente para garantir segurança no POST)
        $user = auth()->user();
        if (!$user->hasRole('Administrador')) {
            $allowed = false;
            if ($user->hasRole('ResponsavelTime') && $equipeCampeonato->equipe->time->tim_user_id === $user->id) {
                $allowed = true;
            }
            if ($user->hasRole('ComissaoTecnica') && $equipeCampeonato->equipe->time->tim_id === $user->time_id) {
                $allowed = true;
            }
            if (!$allowed) {
                abort(403, 'Ação não autorizada.');
            }
        }

        // Validação Adicional: O atleta pertence mesmo ao time desta equipe?
        $atleta = Atleta::findOrFail($request->atleta_id);

        // Cuidado: $equipeCampeonato->equipe->time é o objeto time.
        if ($atleta->atl_tim_id !== $equipeCampeonato->equipe->time->tim_id) {
            return back()->with('error', 'Este atleta não pertence ao time desta equipe.');
        }

        // Validação: Cartão Impresso
        if (!$atleta->cartaoImpresso(date('Y'))) {
            return back()->with('error', 'Este atleta não possui o cartão da liga impresso para o ano atual (' . date('Y') . '). Impressão necessária para adicionar ao elenco.');
        }

        // Validação de Idade (Regra: Atleta pode jogar em categoria mais velha, mas não mais nova)
        // Check se o atleta cumpre a idade máxima da categoria do campeonato
        $categoriaEquipe = $equipeCampeonato->equipe->categoria;
        $maxAge = $categoriaEquipe ? $categoriaEquipe->cto_idade_maxima : null;

        if (!is_null($maxAge)) {
            // $anoCampeonato = $equipeCampeonato->campeonato->cpo_ano ?? date('Y');
            // $anoNascimento = date('Y', strtotime($atleta->atl_dt_nasc));
            // $idadeNoAno = $anoCampeonato - $anoNascimento;

            // Usando Idade Real (hoje)
            $idadeReal = \Carbon\Carbon::parse($atleta->atl_dt_nasc)->age;

            if ($idadeReal > $maxAge) {
                return back()->with('error', "Este atleta não possui idade permitida para esta categoria ({$categoriaEquipe->cto_nome}). Idade atual: $idadeReal anos. Limite: $maxAge anos.");
            }
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
        if (!$user->hasRole('Administrador')) {
            $allowed = false;
            if ($user->hasRole('ResponsavelTime') && $eqpCpo->equipe->time->tim_user_id === $user->id) {
                $allowed = true;
            }
            if ($user->hasRole('ComissaoTecnica') && $eqpCpo->equipe->time->tim_id === $user->time_id) {
                $allowed = true;
            }
            if (!$allowed) {
                abort(403, 'Ação não autorizada.');
            }
        }

        $elenco->delete();

        return back()->with('success', 'Atleta removido do elenco.');
    }
}
