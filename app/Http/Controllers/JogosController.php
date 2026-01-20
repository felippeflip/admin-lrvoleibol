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
        $query = DB::table('wp_posts')
            ->where('post_type', 'event_listing')
            // Join local table to get Status
            ->leftJoin('jogos', 'wp_posts.ID', '=', 'jogos.jgo_wp_id')
            ->select('wp_posts.*', 'jogos.jgo_status', 'jogos.jgo_res_status', 'jogos.jgo_res_usuario_id', 'jogos.jgo_id as local_id', 'jogos.jgo_apontador');

        $user = Auth::user();
        if ($user->hasRole('Juiz') && !$user->hasRole('Administrador')) {
            $query->where(function ($q) use ($user) {
                $q->where('jogos.jgo_arbitro_principal', $user->id)
                    ->orWhere('jogos.jgo_arbitro_secundario', $user->id)
                    ->orWhere('jogos.jgo_apontador', $user->id);
            });
        }

        // Filters
        if ($request->filled('titulo')) {
            $query->where('post_title', 'like', '%' . $request->titulo . '%');
        }

        if ($request->filled('status')) {
            // Removed 'sem_status' logic as requested
            $query->where('jogos.jgo_status', $request->status);
        }

        if ($request->filled('campeonato_id')) {
            $camp = Campeonato::find($request->campeonato_id);
            if ($camp) {
                $termId = $camp->cpo_term_tx_id;
                $query->whereExists(function ($sub) use ($termId) {
                    $sub->select(DB::raw(1))
                        ->from('wp_term_relationships')
                        ->whereColumn('wp_term_relationships.object_id', 'wp_posts.ID')
                        ->where('term_taxonomy_id', $termId);
                });
            }
        }

        if ($request->filled('ginasio_id')) {
            $gin = Ginasio::find($request->ginasio_id);
            if ($gin) {
                // Busca pelo nome do ginasio no meta location
                $locName = $gin->gin_nome;
                $query->whereExists(function ($sub) use ($locName) {
                    $sub->select(DB::raw(1))
                        ->from('wp_postmeta')
                        ->whereColumn('post_id', 'wp_posts.ID')
                        ->where('meta_key', '_event_location')
                        ->where('meta_value', 'like', "{$locName}%");
                });
            }
        }

        // Period Date Filter
        if ($request->filled('data_inicio')) {
            $start = $request->data_inicio . ' 00:00:00';
            $query->whereExists(function ($sub) use ($start) {
                $sub->select(DB::raw(1))
                    ->from('wp_postmeta')
                    ->whereColumn('post_id', 'wp_posts.ID')
                    ->where('meta_key', '_event_start_date')
                    ->where('meta_value', '>=', $start);
            });
        }

        if ($request->filled('data_fim')) {
            $end = $request->data_fim . ' 23:59:59';
            $query->whereExists(function ($sub) use ($end) {
                $sub->select(DB::raw(1))
                    ->from('wp_postmeta')
                    ->whereColumn('post_id', 'wp_posts.ID')
                    ->where('meta_key', '_event_start_date')
                    ->where('meta_value', '<=', $end);
            });
        }

        // Pagination with sort by date meta (DESC: Maior para Menor)
        $jogos = $query->orderByRaw("(SELECT meta_value FROM wp_postmeta WHERE post_id = wp_posts.ID AND meta_key = '_event_start_date' LIMIT 1) DESC")
            ->paginate(15)
            ->appends($request->all());

        // Transform items to attach Meta and Relations
        $jogos->getCollection()->transform(function ($jogo) {
            // Populate Meta
            $meta = DB::table('wp_postmeta')->where('post_id', $jogo->ID)->get()->keyBy('meta_key')->toArray();
            $jogo->meta = $meta;

            // Populate Taxonomy
            $termRelationships = DB::table('wp_term_relationships')->where('object_id', $jogo->ID)->get()->toArray();
            foreach ($termRelationships as &$relationship) {
                $termTaxonomy = DB::table('wp_term_taxonomy')->where('term_taxonomy_id', $relationship->term_taxonomy_id)->first();
                if ($termTaxonomy) {
                    $relationship->term_taxonomy = $termTaxonomy;
                    $term = DB::table('wp_terms')->where('term_id', $termTaxonomy->term_id)->first();
                    if ($term)
                        $relationship->term_taxonomy->term = $term;
                }
            }
            $jogo->term_relationships = $termRelationships;

            // Default Status Display logic if null
            if (!$jogo->jgo_status) {
                $jogo->jgo_status = 'ativo'; // Default for WP
            }

            return $jogo;
        });

        $campeonatos = Campeonato::orderBy('cpo_nome')->get();
        $ginasios = Ginasio::orderBy('gin_nome')->get();

        return view('jogos.index', compact('jogos', 'campeonatos', 'ginasios'));
    }

    public function index_dashboard(Request $request)
    {
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
                $com_apontamento = $jogosDoCampeonato->whereNotNull('jgo_res_status')->count();

                // Calculated: Finished games that HAVE NO result submitted yet
                $sem_apontamento = $jogosDoCampeonato->where('jgo_dt_jogo', '<', now()->format('Y-m-d'))
                    ->whereNull('jgo_res_status')
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
        }

        // --- 2. JUIZ ---
        if ($user->hasRole('Juiz') || $user->is_arbitro) {
            // Games where user is selected assigned
            $meusJogos = Jogo::with(['mandante.campeonato', 'mandante.equipe', 'visitante.equipe', 'ginasio'])
                ->where(function ($q) use ($user) {
                $q->where('jgo_arbitro_principal', $user->id)
                    ->orWhere('jgo_arbitro_secundario', $user->id)
                    ->orWhere('jgo_apontador', $user->id);
            })->get();

            $juizStats = [
                'total_participacao' => $meusJogos->count(),
                'novos' => $meusJogos->where('jgo_dt_jogo', '>=', now()->format('Y-m-d'))->count(),
                'realizados' => $meusJogos->where('jgo_dt_jogo', '<', now()->format('Y-m-d'))->count(),
            ];

            // Pass the listing of upcoming games for convenience if needed, 
            // but the prompt asked for "Quantidade", so stats focused.
            // I'll pass the upcoming games collection just in case we want to show a list.
            $data['juizStats'] = $juizStats;
            $data['juizJogosFuturos'] = $meusJogos->where('jgo_dt_jogo', '>=', now()->format('Y-m-d'));
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

                $jogosQuery = Jogo::with(['mandante.campeonato', 'mandante.equipe', 'visitante.equipe', 'ginasio'])
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
                     $jogosQuery->where(function($q) use ($term) {
                         $q->where('jgo_id', 'like', "%{$term}%")
                           ->orWhereHas('mandante.campeonato', function($sq) use ($term){
                               $sq->where('cpo_nome', 'like', "%{$term}%");
                           });
                     });
                }

                $timeJogos = $jogosQuery->orderBy('jgo_dt_jogo', 'desc')->paginate(10)->appends($request->all());
                
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
        $juizes = User::where('is_arbitro', true)->get();
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
        // $id is the WP ID (because route uses WP ID)
        $jogo = WpPosts::with(['eventTypes.term', 'eventCategories.term', 'meta'])->findOrFail($id);

        $juizes = User::where('is_arbitro', true)->get();
        $campeonatos = Campeonato::where('cpo_ativo', true)->orderBy('cpo_nome')->get();
        $ginasios = Ginasio::orderBy('gin_nome')->get();
        $categorias = Categoria::orderBy('cto_nome')->get();

        $eventNumber = $jogo->getMetaValue('_event_number');

        // --- 1. Identify Taxonomy Data ---
        $selectedCampeonatoId = null;
        if ($jogo->eventTypes->isNotEmpty()) {
            $termId = $jogo->eventTypes->first()->term_taxonomy_id;
            $camp = Campeonato::where('cpo_term_tx_id', $termId)->first();
            if ($camp)
                $selectedCampeonatoId = $camp->cpo_id;
        }

        $selectedCategoriaId = null;
        if ($jogo->eventCategories->isNotEmpty()) {
            $termId = $jogo->eventCategories->first()->term_taxonomy_id;
            $cat = Categoria::where('cto_term_tx_id', $termId)->first();
            if ($cat)
                $selectedCategoriaId = $cat->cto_id;
        }

        // --- 2. Retrieve Local Data from Table 'jogos' (Primary Source) ---
        // Priority 1: Check new jgo_wp_id column
        $localJogo = Jogo::where('jgo_wp_id', $id)->first();

        // Priority 2: Check standard meta '_local_jogo_id' (Migration/Fallback)
        if (!$localJogo) {
            $localJogoId = $jogo->getMetaValue('_local_jogo_id');
            if ($localJogoId) {
                $localJogo = Jogo::find($localJogoId);
                // Self-Healing: If found by meta, save the WP ID to local table immediately
                if ($localJogo && !$localJogo->jgo_wp_id) {
                    $localJogo->update(['jgo_wp_id' => $id]);
                    Log::info("Migrated Jogo {$localJogo->jgo_id} to include jgo_wp_id {$id}");
                }
            }
        }

        if ($localJogo) {
            // Data from Local Table
            $mandanteId = $localJogo->jgo_eqp_cpo_mandante_id;
            $visitanteId = $localJogo->jgo_eqp_cpo_visitante_id;
            $ginasioId = $localJogo->jgo_local_jogo_id;
            $juizPrincipalId = $localJogo->jgo_arbitro_principal;
            $juizLinha1Id = $localJogo->jgo_arbitro_secundario;
            $juizLinha2Id = $localJogo->jgo_apontador;

            $dataJogo = $localJogo->jgo_dt_jogo ? \Carbon\Carbon::parse($localJogo->jgo_dt_jogo)->format('Y-m-d') : null;
            $horaJogo = $localJogo->jgo_hora_jogo ? \Carbon\Carbon::parse($localJogo->jgo_hora_jogo)->format('H:i') : null;

        } else {
            // Data from WP Meta (Redundancy/Legacy)
            $mandanteId = $jogo->getMetaValue('_mandante_id');
            $visitanteId = $jogo->getMetaValue('_visitante_id');
            $ginasioId = $jogo->getMetaValue('_ginasio_id');
            $juizPrincipalId = $jogo->getMetaValue('_juiz_principal');
            $juizLinha1Id = $jogo->getMetaValue('_juiz_linha1');
            $juizLinha2Id = $jogo->getMetaValue('_juiz_linha2');

            $dtMeta = $jogo->getMetaValue('_event_start_date');
            $dataJogo = $dtMeta ? \Carbon\Carbon::parse($dtMeta)->format('Y-m-d') : null;

            $horaTime = $jogo->getMetaValue('_event_start_time');
            $horaJogo = $horaTime ? \Carbon\Carbon::parse($horaTime)->format('H:i') : null;
        }

        // --- 3. Inference / Migration (for legacy data with no IDs) ---
        if ((!$mandanteId || !$visitanteId) && $selectedCampeonatoId) {
            // Use same robust logic (stripped debug logs for brevity, but functionality remains)
            $parts = preg_split('/\s*(X|x|vs|VS)\s*/', $jogo->post_title);
            if (count($parts) === 2) {
                $nameMandante = trim($parts[0]);
                $nameVisitante = trim($parts[1]);
                $candidates = EquipeCampeonato::with(['equipe.time'])
                    ->where('cpo_fk_id', $selectedCampeonatoId)
                    ->get();
                $findTeamId = function ($name) use ($candidates) {
                    $name = trim($name);
                    if (strlen($name) < 3)
                        return null;
                    foreach ($candidates as $pivot) {
                        $eqp = $pivot->equipe;
                        $time = $eqp->time;
                        $namesToCheck = [$eqp->eqp_nome_detalhado, $time->tim_nome ?? '', $time->tim_nome_abre ?? '', $time->tim_sigla ?? ''];
                        foreach ($namesToCheck as $candidateName) {
                            if ($candidateName && strcasecmp($name, $candidateName) === 0)
                                return $pivot->eqp_cpo_id;
                        }
                    }
                    // Partial
                    foreach ($candidates as $pivot) {
                        $eqp = $pivot->equipe;
                        $time = $eqp->time;
                        $namesToCheck = [$eqp->eqp_nome_detalhado, $time->tim_nome ?? '', $time->tim_nome_abre ?? ''];
                        foreach ($namesToCheck as $candidateName) {
                            if ($candidateName && (stripos($candidateName, $name) !== false || stripos($name, $candidateName) !== false)) {
                                return $pivot->eqp_cpo_id;
                            }
                        }
                    }
                    return null;
                };
                if (!$mandanteId)
                    $mandanteId = $findTeamId($nameMandante);
                if (!$visitanteId)
                    $visitanteId = $findTeamId($nameVisitante);
            }
        }

        if (!$ginasioId) {
            $loc = $jogo->getMetaValue('_event_location');
            if ($loc) {
                $parts = explode(' - ', $loc);
                $ginName = trim($parts[0]);
                $gin = Ginasio::where('gin_nome', $ginName)->first();
                if ($gin)
                    $ginasioId = $gin->gin_id;
            }
        }

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
        // Priority: jgo_wp_id -> meta -> create
        $localJogo = Jogo::where('jgo_wp_id', $id)->first();
        if (!$localJogo) {
            $wpPost = WpPosts::with('meta')->find($id);
            $localJogoId = $wpPost ? $wpPost->getMetaValue('_local_jogo_id') : null;
            if ($localJogoId) {
                $localJogo = Jogo::find($localJogoId);
            }
        }

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

        // Status Logic: If date changed, maybe check if it's inactive? 
        // Or trust the next 'index' status checker.

        if ($localJogo) {
            $localJogo->update($data);
            // Ensure link
            if (!$localJogo->jgo_wp_id)
                $localJogo->update(['jgo_wp_id' => $id]);
        } else {
            // Create new local record for this legacy game
            $data['jgo_wp_id'] = $id;
            $localJogo = Jogo::create($data);
        }

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

    private function updateThumbnail($postId)
    {
        try {
            Http::post('https://lrvoleibol.com.br/wp-json/custom/v1/update_event_thumbnail', [
                'post_id' => $postId,
                'attachment_id' => 4132
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar thumbnail em update: ' . $e->getMessage());
        }
    }


    public function destroy($id)
    {
        if (!Auth::user()->hasRole('Administrador')) {
            return redirect()->route('jogos.index')->with('error', 'Acesso não autorizado.');
        }

        $post = WpPosts::findOrFail($id);

        // Also delete local jogo if linked
        $localJogo = Jogo::where('jgo_wp_id', $id)->first();
        if (!$localJogo) {
            $localJogoId = $post->getMetaValue('_local_jogo_id');
            if ($localJogoId)
                $localJogo = Jogo::find($localJogoId);
        }

        if ($localJogo) {
            $localJogo->delete();
        }

        $post->delete();

        WpPostmeta::where('post_id', $id)->delete();
        Wp_Term_Relationships::where('object_id', $id)->delete();

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
