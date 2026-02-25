<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wp_Term_Taxonomy;
use App\Models\WpPosts;
use App\Models\WpPostmeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Carbon;
use League\Csv\Reader;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Wp_Term_Relationships;
use Illuminate\Support\Facades\Http;
use App\Models\Campeonato;
use App\Models\Ginasio;
use App\Models\Categoria;
use App\Models\EquipeCampeonato;
use App\Models\Jogo;
use App\Models\Equipe;
use App\Services\JogoService;
use App\Services\WordpressGameService;
use App\Models\Time;

class JogosController extends Controller
{
    public function index(Request $request)
    {
        // 1. Auto-update Status Local
        Jogo::where('jgo_dt_jogo', '<', Carbon::now()->format('Y-m-d'))
            ->where('jgo_status', '!=', 'inativo')
            ->update(['jgo_status' => 'inativo']);

        // 2. Build Query
        $query = Jogo::with([
            'mandante.campeonato',
            'mandante.equipe',
            'visitante.equipe',
            'ginasio',
            'arbitroPrincipal',
            'arbitroSecundario',
            'apontador'
        ]);

        $user = Auth::user();
        if ($user->hasRole('Juiz') && !$user->hasRole('Administrador')) {
            $query->where(function ($q) use ($user) {
                $q->where('jgo_arbitro_principal', $user->id)
                    ->orWhere('jgo_arbitro_secundario', $user->id)
                    ->orWhere('jgo_apontador', $user->id);
            });
        }

        // Filters
        // Título não existe localmente, buscaremos pelo nome das equipes
        if ($request->filled('titulo')) {
            $term = $request->titulo;
            $query->where(function ($q) use ($term) {
                $q->whereHas('mandante.equipe', function ($sq) use ($term) {
                    $sq->where('eqp_nome_detalhado', 'like', "%{$term}%");
                })->orWhereHas('visitante.equipe', function ($sq) use ($term) {
                    $sq->where('eqp_nome_detalhado', 'like', "%{$term}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('jgo_status', $request->status);
        }

        if ($request->filled('campeonato_id')) {
            $campId = $request->campeonato_id;
            $query->whereHas('mandante', function ($q) use ($campId) {
                $q->where('cpo_fk_id', $campId);
            });
        }

        if ($request->filled('ginasio_id')) {
            $query->where('jgo_local_jogo_id', $request->ginasio_id);
        }

        if ($request->filled('data_inicio')) {
            $query->where('jgo_dt_jogo', '>=', $request->data_inicio);
        }

        if ($request->filled('data_fim')) {
            $query->where('jgo_dt_jogo', '<=', $request->data_fim);
        }

        // Pagination with sort by date local
        $jogos = $query->orderBy('jgo_dt_jogo', 'desc')
            ->orderBy('jgo_hora_jogo', 'desc')
            ->paginate(15)
            ->appends($request->all());

        // Transform items to attach Meta and Relations as expected by the view
        $jogos->getCollection()->transform(function ($jogo) {
            $nomeMandante = $jogo->mandante->equipe->eqp_nome_detalhado ?? 'Mandante';
            $nomeVisitante = $jogo->visitante->equipe->eqp_nome_detalhado ?? 'Visitante';
            $postTitle = "{$nomeMandante} X {$nomeVisitante}";

            $local = "Local Indefinido";
            if ($jogo->ginasio) {
                $gin = $jogo->ginasio;
                $endereco = $gin->gin_endereco;
                if ($gin->gin_numero)
                    $endereco .= ", " . $gin->gin_numero;
                if ($gin->gin_bairro)
                    $endereco .= " - " . $gin->gin_bairro;
                $local = "{$gin->gin_nome} - {$endereco}";
            }

            $jogo->ID = $jogo->jgo_wp_id ?: $jogo->jgo_id;
            $jogo->local_id = $jogo->jgo_id;
            $jogo->post_title = $postTitle;

            $tempRelations = [];
            if ($jogo->mandante && $jogo->mandante->campeonato) {
                $rel = new \stdClass();
                $termTaxonomy = new \stdClass();
                $termTaxonomy->taxonomy = 'event_listing_type';
                $term = new \stdClass();
                $term->name = $jogo->mandante->campeonato->cpo_nome;
                $termTaxonomy->term = $term;
                $rel->term_taxonomy = $termTaxonomy;
                $tempRelations[] = $rel;
            }
            $jogo->setAttribute('term_relationships', $tempRelations);

            $jogo->meta = [
                '_event_number' => (object) ['meta_value' => 'ID-' . $jogo->jgo_id], // Não guardamos event_number no DB Jogo, usando ID.
                '_event_title' => (object) ['meta_value' => $postTitle],
                '_event_location' => (object) ['meta_value' => $local],
                '_event_start_date' => (object) ['meta_value' => $jogo->jgo_dt_jogo],
                '_event_start_time' => (object) ['meta_value' => $jogo->jgo_hora_jogo],
            ];

            // Use the eager loaded relationships
            $jogo->arbitro_principal_nome = $jogo->arbitroPrincipal->name ?? null;
            $jogo->arbitro_secundario_nome = $jogo->arbitroSecundario->name ?? null;
            $jogo->apontador_nome = $jogo->apontador->name ?? null;

            return $jogo;
        });

        $campeonatos = Campeonato::orderBy('cpo_nome')->get();
        $ginasios = Ginasio::orderBy('gin_nome')->get();

        return view('jogos.index', compact('jogos', 'campeonatos', 'ginasios'));
    }

    public function index_dashboard(Request $request)
    {
        // Auto-update Status for Dashboard (Sync with index logic)
        Jogo::where('jgo_dt_jogo', '<', Carbon::now()->format('Y-m-d'))
            ->where('jgo_status', '!=', 'inativo')
            ->update(['jgo_status' => 'inativo']);

        $user = Auth::user();
        $data = [];

        // --- 1. ADMINISTRADOR ---
        if ($user->hasRole('Administrador')) {
            $campeonatos = Campeonato::where('cpo_ativo', true)->get();
            $adminStats = [];

            foreach ($campeonatos as $camp) {
                // Get games related to this championship
                // We check 'mandante' relationship to find the championship
                $jogosDoCampeonato = Jogo::whereHas('mandante', function ($q) use ($camp) {
                    $q->where('cpo_fk_id', $camp->cpo_id);
                })->get();

                $novos = $jogosDoCampeonato->where('jgo_dt_jogo', '>=', now()->format('Y-m-d'))->count();
                $finalizados_total = $jogosDoCampeonato->where('jgo_dt_jogo', '<', now()->format('Y-m-d'))->count();
                $com_apontamento = $jogosDoCampeonato->whereIn('jgo_res_status', ['pendente', 'aprovado'])->count();

                // Calculated: Finished games that HAVE NO result submitted yet (Null or invalid/empty or 'nao_informado')
                $sem_apontamento = $jogosDoCampeonato->where('jgo_dt_jogo', '<', now()->format('Y-m-d'))
                    ->filter(function ($jogo) {
                        return !in_array($jogo->jgo_res_status, ['pendente', 'aprovado']);
                    })
                    ->count();

                $adminStats[] = [
                    'id' => $camp->cpo_id,
                    'campeonato' => $camp->cpo_nome,
                    'novos' => $novos,
                    'finalizados' => $finalizados_total, // Just past games
                    'com_apontamento' => $com_apontamento,
                    'sem_apontamento' => $sem_apontamento
                ];
            }
            $data['adminStats'] = $adminStats;

            // Dashboard Cards: Games from -7 to +30 days (Expanded to show more games)
            $startDate = now()->subDays(7)->startOfDay();
            $endDate = now()->addDays(30)->endOfDay();

            $adminJogos = Jogo::with(['mandante.campeonato', 'mandante.equipe', 'visitante.equipe', 'ginasio', 'arbitroPrincipal', 'arbitroSecundario', 'apontador'])
                ->whereBetween('jgo_dt_jogo', [$startDate, $endDate])
                ->orderBy('jgo_dt_jogo')
                ->orderBy('jgo_hora_jogo')
                ->get();

            $data['adminJogos'] = $adminJogos;
        }

        // --- 2. JUIZ ---
        if ($user->hasRole('Juiz') || $user->is_arbitro) {
            // Base Query for User's Games
            $juizQuery = Jogo::with(['mandante.campeonato', 'mandante.equipe', 'visitante.equipe', 'ginasio', 'arbitroPrincipal', 'arbitroSecundario', 'apontador'])
                ->where(function ($q) use ($user) {
                    $q->where('jgo_arbitro_principal', $user->id)
                        ->orWhere('jgo_arbitro_secundario', $user->id)
                        ->orWhere('jgo_apontador', $user->id);
                });

            // 2.1 Calculate Stats (using a clone to not affect the query object)
            $statsQuery = clone $juizQuery;
            $meusJogos = $statsQuery->get();

            $juizStats = [
                'total_participacao' => $meusJogos->count(),
                'novos' => $meusJogos->where('jgo_dt_jogo', '>=', now()->format('Y-m-d'))->count(),
                'realizados' => $meusJogos->where('jgo_dt_jogo', '<', now()->format('Y-m-d'))->count(),
            ];
            $data['juizStats'] = $juizStats;

            // 2.2 Apply Filters and Pagination for the List
            $statusFilter = $request->input('status', 'ativo'); // Default to 'ativo'

            if ($statusFilter && $statusFilter !== 'todos') {
                $juizQuery->where('jgo_status', $statusFilter);
            }

            if ($request->filled('search')) {
                $term = $request->search;
                // Search by ID or Championship Name
                $juizQuery->where(function ($q) use ($term) {
                    $q->where('jgo_id', 'like', "%{$term}%")
                        ->orWhereHas('mandante.campeonato', function ($sq) use ($term) {
                            $sq->where('cpo_nome', 'like', "%{$term}%");
                        });
                });
            }

            // Pagination - Changed to get() for card view
            $juizJogos = $juizQuery->orderBy('jgo_dt_jogo', 'desc')
                ->get(); // Using get() for card view instead of pagination

            $data['juizJogos'] = $juizJogos;

            // Shared filter for View (if not already set)
            if (!isset($data['statusFilter'])) {
                $data['statusFilter'] = $statusFilter;
            }
        }

        // --- 3. RESPONSAVEL PELO TIME ---
        if (($user->hasRole('ResponsavelTime') || $user->is_resp_time) && !$user->hasRole('Administrador')) {
            $time = Time::where('tim_user_id', $user->id)->first();

            if ($time) {
                // Find all EquipeCampeonato entries for this Time
                $equipeIds = DB::table('equipes')
                    ->where('eqp_time_id', $time->tim_id)
                    ->join('equipe_campeonato', 'equipes.eqp_id', '=', 'equipe_campeonato.eqp_fk_id')
                    ->pluck('equipe_campeonato.eqp_cpo_id');

                $jogosQuery = Jogo::with(['mandante.campeonato', 'mandante.equipe', 'visitante.equipe', 'ginasio', 'arbitroPrincipal', 'arbitroSecundario', 'apontador'])
                    ->where(function ($query) use ($equipeIds) {
                        $query->whereIn('jgo_eqp_cpo_mandante_id', $equipeIds)
                            ->orWhereIn('jgo_eqp_cpo_visitante_id', $equipeIds);
                    });

                // Stats Calculation (before pagination filtering, or separately?)
                // The stats should probably reflect the total picture, not just the filtered list.
                // Re-instantiating query for stats or cloning.
                $statsQuery = clone $jogosQuery;
                $allJogosForStats = $statsQuery->get();

                $timeStats = [
                    'escalado_total' => $allJogosForStats->count(),
                    'concluidos' => $allJogosForStats->where('jgo_dt_jogo', '<', now()->format('Y-m-d'))->count(),
                    'proximos' => $allJogosForStats->where('jgo_dt_jogo', '>=', now()->format('Y-m-d'))->count(),
                ];

                $data['timeStats'] = $timeStats;

                // Filters for the List
                // "status filtrado ativo como default"
                $statusFilter = $request->input('status', 'ativo');

                if ($statusFilter) {
                    // Assuming 'ativo' means future games or explicitly 'ativo' status? 
                    // Use 'jgo_status' column if that's what stores it.
                    // In index(), we saw: Jogo::where('jgo_dt_jogo', '<', now())->update(['jgo_status' => 'inativo']);
                    // So 'ativo' likely corresponds to 'jgo_status' = 'ativo'.
                    if ($statusFilter !== 'todos') {
                        $jogosQuery->where('jgo_status', $statusFilter);
                    }
                }

                if ($request->filled('search')) {
                    $term = $request->search;
                    // Search by ID/Number
                    $jogosQuery->where(function ($q) use ($term) {
                        $q->where('jgo_id', 'like', "%{$term}%")
                            ->orWhereHas('mandante.campeonato', function ($sq) use ($term) {
                                $sq->where('cpo_nome', 'like', "%{$term}%");
                            });
                    });
                }

                $timeJogos = $jogosQuery->orderBy('jgo_dt_jogo', 'asc')->get(); // Changed to get() for card view

                $data['timeJogos'] = $timeJogos;
                $data['statusFilter'] = $statusFilter;

            } else {
                $data['timeStats'] = null; // Has role but no team assigned
            }
        }

        // Fallback or Shared Data (e.g. recent games list for everyone or just admin?)
        // The original code returned 'jogos' (all synced properties).
        // Since we are creating custom dashboards, we might standardise what 'jogos' variable holds 
        // OR just rely on the new Stat variables.
        // For backward compatibility with the View (if I reuse parts), I should ensure strictly needed variables are passed.
        // But since I'm rewriting the view, I can control it.

        return view('dashboard', $data);
    }


    public function create()
    {
        $juizes = User::role('Juiz')->where('active', true)->orderBy('name')->get();
        $campeonatos = Campeonato::where('cpo_ativo', true)->orderBy('cpo_nome')->get();
        $ginasios = Ginasio::orderBy('gin_nome')->get();
        $categorias = Categoria::orderBy('cto_nome')->get();

        return view('jogos.create', compact('campeonatos', 'ginasios', 'categorias', 'juizes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_number' => 'required|integer',
            'campeonato_id' => 'required|exists:campeonatos,cpo_id',
            'mandante_id' => 'required|exists:equipe_campeonato,eqp_cpo_id|different:visitante_id',
            'visitante_id' => 'required|exists:equipe_campeonato,eqp_cpo_id',
            'categoria_id' => 'required|exists:categorias,cto_id',
            'ginasio_id' => 'required|exists:ginasios,gin_id',
            'data_jogo' => 'required|date_format:Y-m-d',
            'hora_jogo' => 'required|date_format:H:i',
            'juiz_principal' => 'nullable|exists:users,id',
            'juiz_linha1' => 'nullable|exists:users,id',
            'juiz_linha2' => 'nullable|exists:users,id',
        ]);

        try {
            // 1. Create Local Jogo
            $jogoService = new JogoService();
            $jogo = $jogoService->create([
                'mandante_id' => $request->mandante_id,
                'visitante_id' => $request->visitante_id,
                'data_jogo' => $request->data_jogo,
                'hora_jogo' => $request->hora_jogo,
                'ginasio_id' => $request->ginasio_id,
                'juiz_principal_id' => $request->juiz_principal,
                'juiz_secundario_id' => $request->juiz_linha1,
                'apontador_id' => $request->juiz_linha2,
            ]);

            $campeonato = Campeonato::find($request->campeonato_id);
            $categoria = Categoria::find($request->categoria_id);

            // 2. Sync to WordPress
            $wpService = new WordpressGameService();
            $wpPostId = $wpService->sync($jogo, [
                'event_number' => $request->event_number,
                'event_type' => $campeonato->cpo_term_tx_id,
                'event_category' => $categoria->cto_term_tx_id,
            ]);

            // 3. Link WP ID back to Local Jogo (Bidirectional Link)
            if ($wpPostId) {
                $jogo->update(['jgo_wp_id' => $wpPostId]);
            }

            return redirect()->route('jogos.index')->with('success', 'Jogo criado com sucesso e sincronizado!');

        } catch (\Exception $e) {
            Log::error("Erro ao criar jogo: " . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return redirect()->back()->with('error', 'Erro ao criar o jogo: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        // 1. Encontra Jogo Local sempre primeiro (id pode ser local ou wp)
        $localJogo = Jogo::where('jgo_wp_id', $id)->orWhere('jgo_id', $id)->firstOrFail();

        $juizes = User::role('Juiz')->where('active', true)->orderBy('name')->get();
        $campeonatos = Campeonato::where('cpo_ativo', true)->orderBy('cpo_nome')->get();
        $ginasios = Ginasio::orderBy('gin_nome')->get();
        $categorias = Categoria::orderBy('cto_nome')->get();

        // 2. Busca o legadão em wp_posts para extrair meta legacy (event_number, categoria) 
        // caso existam de quando os DBs não eram isolados.
        $wpPost = WpPosts::with(['eventTypes.term', 'eventCategories.term', 'meta'])->find($localJogo->jgo_wp_id ?: $id);

        $eventNumber = $wpPost ? $wpPost->getMetaValue('_event_number') : $localJogo->jgo_id;

        $selectedCampeonatoId = $localJogo->mandante->cpo_fk_id ?? null;
        if (!$selectedCampeonatoId && $wpPost && $wpPost->eventTypes->isNotEmpty()) {
            $termId = $wpPost->eventTypes->first()->term_taxonomy_id;
            $camp = Campeonato::where('cpo_term_tx_id', $termId)->first();
            if ($camp)
                $selectedCampeonatoId = $camp->cpo_id;
        }

        $selectedCategoriaId = null;
        if ($wpPost && $wpPost->eventCategories->isNotEmpty()) {
            $termId = $wpPost->eventCategories->first()->term_taxonomy_id;
            $cat = Categoria::where('cto_term_tx_id', $termId)->first();
            if ($cat)
                $selectedCategoriaId = $cat->cto_id;
        }

        $mandanteId = $localJogo->jgo_eqp_cpo_mandante_id;
        $visitanteId = $localJogo->jgo_eqp_cpo_visitante_id;
        $ginasioId = $localJogo->jgo_local_jogo_id;
        $juizPrincipalId = $localJogo->jgo_arbitro_principal;
        $juizLinha1Id = $localJogo->jgo_arbitro_secundario;
        $juizLinha2Id = $localJogo->jgo_apontador;

        $dataJogo = $localJogo->jgo_dt_jogo ? \Carbon\Carbon::parse($localJogo->jgo_dt_jogo)->format('Y-m-d') : null;
        $horaJogo = $localJogo->jgo_hora_jogo ? \Carbon\Carbon::parse($localJogo->jgo_hora_jogo)->format('H:i') : null;

        // Faking object format param that Edit blade needs just for form Action ID
        $jogo = clone $localJogo;
        $jogo->ID = $localJogo->jgo_wp_id ?: $localJogo->jgo_id;

        return view('jogos.edit', compact(
            'jogo',
            'campeonatos',
            'ginasios',
            'categorias',
            'juizes',
            'eventNumber',
            'juizPrincipalId',
            'juizLinha1Id',
            'juizLinha2Id',
            'mandanteId',
            'visitanteId',
            'ginasioId',
            'selectedCampeonatoId',
            'selectedCategoriaId',
            'dataJogo',
            'horaJogo'
        ));
    }

    public function update(Request $request, $id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            return redirect()->route('jogos.index')->with('error', 'Acesso não autorizado.');
        }

        $request->validate([
            'event_number' => 'required|integer',
            'campeonato_id' => 'required|exists:campeonatos,cpo_id',
            'mandante_id' => 'required|exists:equipe_campeonato,eqp_cpo_id|different:visitante_id',
            'visitante_id' => 'required|exists:equipe_campeonato,eqp_cpo_id',
            'categoria_id' => 'required|exists:categorias,cto_id',
            'ginasio_id' => 'required|exists:ginasios,gin_id',
            'data_jogo' => 'required|date_format:Y-m-d',
            'hora_jogo' => 'required|date_format:H:i',
            'juiz_principal' => 'nullable|exists:users,id',
            'juiz_linha1' => 'nullable|exists:users,id',
            'juiz_linha2' => 'nullable|exists:users,id',
        ]);

        // 1. Find or Create Local Jogo
        $localJogo = Jogo::where('jgo_wp_id', $id)->orWhere('jgo_id', $id)->firstOrFail();

        // Data to update/create
        $data = [
            'jgo_eqp_cpo_mandante_id' => $request->mandante_id,
            'jgo_eqp_cpo_visitante_id' => $request->visitante_id,
            'jgo_dt_jogo' => $request->data_jogo,
            'jgo_hora_jogo' => $request->hora_jogo,
            'jgo_local_jogo_id' => $request->ginasio_id,
            'jgo_arbitro_principal' => $request->juiz_principal,
            'jgo_arbitro_secundario' => $request->juiz_linha1,
            'jgo_apontador' => $request->juiz_linha2,
        ];

        $localJogo->update($data);

        // 2. Sync to WordPress (Use Service)
        $campeonato = Campeonato::find($request->campeonato_id);
        $categoria = Categoria::find($request->categoria_id);

        $wpService = new WordpressGameService();
        $wpService->sync($localJogo, [
            'event_number' => $request->event_number,
            'event_type' => $campeonato->cpo_term_tx_id,
            'event_category' => $categoria->cto_term_tx_id,
        ], $id); // Pass $id to update

        return redirect()->route('jogos.index')->with('success', 'Jogo atualizado e sincronizado com sucesso!');
    }



    private function deleteEventViaApi($postId)
    {
        try {
            // Tenta chamar o endpoint customizado para deleção correta no WP (limpando cache e arquivos)
            $response = Http::post('https://lrvoleibol.com.br/wp-json/custom/v1/delete_event', [
                'post_id' => $postId
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Erro ao deletar evento via API: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            return redirect()->route('jogos.index')->with('error', 'Acesso não autorizado.');
        }

        $localJogo = Jogo::where('jgo_wp_id', $id)->orWhere('jgo_id', $id)->firstOrFail();

        $wpId = $localJogo->jgo_wp_id;

        // Tenta deletar via API do WordPress (Recomendado)
        if ($wpId) {
            $this->deleteEventViaApi($wpId);
        }

        // Delete do DB isolado Laravel Local
        $localJogo->resultadoSets()->delete();
        $localJogo->delete();

        // Limpeza de tabelas velhas não mais ativas no Laravel, caso existam no DB atual (legado)
        if ($wpId) {
            WpPosts::where('ID', $wpId)->delete();
            WpPostmeta::where('post_id', $wpId)->delete();
            Wp_Term_Relationships::where('object_id', $wpId)->delete();
        }

        return redirect()->route('jogos.index')->with('success', 'Jogo deletado com sucesso');
    }

    public function show()
    {
        return view('jogos.import');
    }


    public function import(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        $path = $request->file('csv_file')->getRealPath();
        $reader = Reader::createFromPath($path, 'r');

        $reader->setDelimiter(';');
        $reader->setEnclosure('"');
        $reader->setHeaderOffset(0);

        $expectedColumns = [
            'numero_jogo',
            'titulo_evento',
            'id_event',
            'tipo_evento',
            'ID_CATEGORY',
            'categoria_evento',
            'local_evento',
            'data_inicio',
            'inicio',
            'ID_USERS',
            'juiz_principal',
            'ID_USERS_1',
            'juiz_linha1',
            'ID_USERS_2',
            'apontador'
        ];

        $records = iterator_to_array($reader->getRecords());
        if (empty($records)) {
            return back()->withErrors(['error' => 'O arquivo CSV está vazio.']);
        }

        foreach ($records as $index => $record) {
            if (empty(implode('', array_map('trim', $record)))) {
                continue;
            }

            if (isset($record['titulo_evento']) && !empty(trim($record['titulo_evento']))) {

                if (count($record) !== count($expectedColumns)) {
                    return back()->withErrors(['error' => "Erro na linha $index: número de colunas incorreto."]);
                }

                foreach ($record as $key => $value) {
                    $val = trim($value);
                    if (substr($val, 1) === '"' && substr($val, -1) === '"') {
                        $val = substr($val, 1, -1);
                    }
                    $detectedEncoding = mb_detect_encoding($val, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
                    if ($detectedEncoding !== 'UTF-8') {
                        $val = mb_convert_encoding($val, 'UTF-8', $detectedEncoding);
                    }
                    $record[$key] = $val;
                }

                try {
                    $post_title = $record['titulo_evento'];
                    $post_name = Str::slug($post_title);
                    $existingPost = DB::table('wp_posts')->where('post_name', $post_name)->first();
                    if ($existingPost) {
                        $post_name .= '-' . (DB::table('wp_posts')->where('post_name', 'like', "$post_name%")->count() + 1);
                    }
                    $post_date = Carbon::now();
                    $wpPostData = [
                        'post_author' => 2,
                        'post_date' => $post_date,
                        'post_date_gmt' => $post_date->copy()->setTimezone('GMT'),
                        'post_content' => '',
                        'post_title' => $post_title,
                        'post_excerpt' => '',
                        'post_status' => 'publish',
                        'comment_status' => 'closed',
                        'ping_status' => 'closed',
                        'post_password' => '',
                        'post_name' => $post_name,
                        'to_ping' => '',
                        'pinged' => '',
                        'post_modified' => $post_date,
                        'post_modified_gmt' => $post_date->copy()->setTimezone('GMT'),
                        'post_content_filtered' => '',
                        'post_parent' => 0,
                        'guid' => '',
                        'menu_order' => 0,
                        'post_type' => 'event_listing',
                        'post_mime_type' => '',
                        'comment_count' => 0,
                    ];

                    $postId = DB::table('wp_posts')->insertGetId($wpPostData);
                    if (!$postId) {
                        return back()->withErrors(['error' => 'Falha ao inserir o post no banco de dados']);
                    }
                    DB::table('wp_posts')->where('ID', $postId)->update([
                        'guid' => "https://lrvoleibol.com.br/event_listing?p=$postId"
                    ]);

                    $metaData = [
                        ['post_id' => $postId, 'meta_key' => '_featured', 'meta_value' => '0'],
                        ['post_id' => $postId, 'meta_key' => '_edit_lock', 'meta_value' => '1720293949:2'],
                        ['post_id' => $postId, 'meta_key' => '_edit_last', 'meta_value' => '2'],
                        ['post_id' => $postId, 'meta_key' => '_view_count', 'meta_value' => '1'],
                        ['post_id' => $postId, 'meta_key' => '_event_title', 'meta_value' => $post_title],
                        ['post_id' => $postId, 'meta_key' => '_event_location', 'meta_value' => $record['local_evento']],
                        ['post_id' => $postId, 'meta_key' => '_event_start_date', 'meta_value' => Carbon::createFromFormat('d/m/Y H:i:s', $record['data_inicio'] . ' ' . $record['inicio'])->format('Y-m-d H:i:s')],
                        ['post_id' => $postId, 'meta_key' => '_event_start_time', 'meta_value' => $record['inicio']],
                        ['post_id' => $postId, 'meta_key' => '_event_number', 'meta_value' => $record['numero_jogo']],
                        ['post_id' => $postId, 'meta_key' => '_juiz_principal', 'meta_value' => $record['ID_USERS']],
                        ['post_id' => $postId, 'meta_key' => '_juiz_linha1', 'meta_value' => $record['ID_USERS_1']],
                        ['post_id' => $postId, 'meta_key' => '_juiz_linha2', 'meta_value' => $record['ID_USERS_2']],
                        ['post_id' => $postId, 'meta_key' => '_event_expiry_date', 'meta_value' => Carbon::createFromFormat('d/m/Y H:i:s', $record['data_inicio'] . ' ' . $record['inicio'])->modify('+1 month')->format('Y-m-d')],
                        ['post_id' => $postId, 'meta_key' => '_thumbnail_id', 'meta_value' => '4132'],
                        ['post_id' => $postId, 'meta_key' => '_event_venue_ids', 'meta_value' => ''],
                        ['post_id' => $postId, 'meta_key' => '_event_banner', 'meta_value' => 'https://lrvoleibol.com.br/wp-content/uploads/2024/07/voleibol.jpg'],
                        ['post_id' => $postId, 'meta_key' => '_cancelled', 'meta_value' => '0'],
                        ['post_id' => $postId, 'meta_key' => '_event_registration_deadline', 'meta_value' => ''],
                        ['post_id' => $postId, 'meta_key' => '_event_country', 'meta_value' => 'Brasil'],
                        ['post_id' => $postId, 'meta_key' => '_registration', 'meta_value' => Auth::user()->email]

                    ];
                    DB::table('wp_postmeta')->insert($metaData);

                    $termRelationships = [
                        ['object_id' => $postId, 'term_taxonomy_id' => $record['id_event'], 'term_order' => 0],
                        ['object_id' => $postId, 'term_taxonomy_id' => $record['ID_CATEGORY'], 'term_order' => 0],
                    ];
                    DB::table('wp_term_relationships')->insert($termRelationships);

                } catch (Exception $e) {
                    return back()->withErrors(['error' => $e->getMessage()]);
                }
            }
            $response = Http::post('https://lrvoleibol.com.br/wp-json/custom/v1/update_event_thumbnail', [
                'post_id' => $postId,
                'attachment_id' => 4132
            ]);

            if (!$response->successful()) {
                Log::error('Erro ao destacar a imagem do evento', [
                    'post_id' => $postId,
                    'attachment_id' => 4132,
                    'response' => $response->json()
                ]);
            }
        }

        return redirect()->route('jogos.import')->with('success', 'Jogos importados com sucesso.');
    }
}
