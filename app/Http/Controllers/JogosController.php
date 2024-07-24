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


class JogosController extends Controller
{
    public function index()
{
    $jogos = DB::table('wp_posts')
                ->where('post_type', 'event_listing')
                ->get()
                ->map(function ($jogo) {
                    // Fetch meta data
                    $meta = DB::table('wp_postmeta')
                            ->where('post_id', $jogo->ID)
                            ->get()
                            ->keyBy('meta_key')
                            ->toArray();
                    $jogo->meta = $meta;

                    // Fetch term relationships and term taxonomy
                    $termRelationships = DB::table('wp_term_relationships')
                                            ->where('object_id', $jogo->ID)
                                            ->get()
                                            ->toArray();

                    foreach ($termRelationships as &$relationship) {
                        $termTaxonomy = DB::table('wp_term_taxonomy')
                                          ->where('term_taxonomy_id', $relationship->term_taxonomy_id)
                                          ->first();

                        if ($termTaxonomy) {
                            $relationship->term_taxonomy = $termTaxonomy;
                            $term = DB::table('wp_terms')
                                      ->where('term_id', $termTaxonomy->term_id)
                                      ->first();

                            if ($term) {
                                $relationship->term_taxonomy->term = $term;
                            }
                        }
                    }

                    $jogo->term_relationships = $termRelationships;

                    return $jogo;
                });
   // dd($jogos->toArray()); die;

    return view('jogos.index', compact('jogos'));
}


// index para apresentar no dashboard -> Para os Luizes verem quais jogos irão participar
public function index_dashboard()
{
    $user = auth()->user();
    $isArbitro = $user->is_arbitro;

    $jogosQuery = DB::table('wp_posts')
                    ->where('post_type', 'event_listing');

    if ($isArbitro) {
        $userId = $user->id;

        $jogosQuery->whereIn('ID', function($query) use ($userId) {
            $query->select('post_id')
                  ->from('wp_postmeta')
                  ->where(function ($query) use ($userId) {
                      $query->where('meta_key', '_juiz_principal')
                            ->where('meta_value', $userId)
                            ->orWhere('meta_key', '_juiz_linha1')
                            ->where('meta_value', $userId)
                            ->orWhere('meta_key', '_juiz_linha2')
                            ->where('meta_value', $userId);
                  });
        });
    }

    $jogos = $jogosQuery->get()
                        ->map(function ($jogo) {
                            // Fetch meta data
                            $meta = DB::table('wp_postmeta')
                                      ->where('post_id', $jogo->ID)
                                      ->get()
                                      ->keyBy('meta_key')
                                      ->toArray();
                            $jogo->meta = $meta;

                            // Fetch term relationships and term taxonomy
                            $termRelationships = DB::table('wp_term_relationships')
                                                  ->where('object_id', $jogo->ID)
                                                  ->get()
                                                  ->toArray();

                            foreach ($termRelationships as &$relationship) {
                                $termTaxonomy = DB::table('wp_term_taxonomy')
                                                  ->where('term_taxonomy_id', $relationship->term_taxonomy_id)
                                                  ->first();

                                if ($termTaxonomy) {
                                    $relationship->term_taxonomy = $termTaxonomy;
                                    $term = DB::table('wp_terms')
                                              ->where('term_id', $termTaxonomy->term_id)
                                              ->first();

                                    if ($term) {
                                        $relationship->term_taxonomy->term = $term;
                                    }
                                }
                            }

                            $jogo->term_relationships = $termRelationships;

                            // Fetch referees
                            $referees = [
                                'principal' => $meta['_juiz_principal']->meta_value ?? null,
                                'line1' => $meta['_juiz_linha1']->meta_value ?? null,
                                'line2' => $meta['_juiz_linha2']->meta_value ?? null,
                            ];

                            foreach ($referees as $key => $userId) {
                                if ($userId) {
                                    $user = DB::table('users')
                                              ->where('ID', $userId)
                                              ->first();
                                    $referees[$key] = $user->apelido ?? 'N/A';
                                } else {
                                    $referees[$key] = 'N/A';
                                }
                            }

                            $jogo->referees = $referees;

                            return $jogo;
                        });

    return view('dashboard', compact('jogos'));
}


    public function create()
    {

        $juizes = \App\Models\User::where('is_arbitro', true)->get();

        $eventTypes = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_type')->get();

        $eventCategorys = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_category')->get();

        return view('jogos.create', compact('eventTypes', 'eventCategorys', 'juizes'));
    }

    public function store(Request $request)
    {

    //dd($request->toArray()); die;

    // Validação dos dados do request
        $request->validate([
        'post_title' => 'required|string|max:100',
        'post_content' => 'required|string',
        'event_type' => 'required|integer',
        'event_category' => 'required|integer',
        'event_online' => 'required|in:yes,no',
        'event_pincode' => 'required_if:event_online,no|string',
        'event_location' => 'required_if:event_online,no|string',
        'event_country' => 'required_if:event_online,no|string',
        'event_banner' => 'required|file|mimes:jpeg,png,jpg,gif,svg',
        'registration_email_url' => 'required|string',
        'video_url' => 'nullable|string',
        'event_start_date' => 'required|date_format:Y-m-d',
        'event_start_time' => 'required|date_format:H:i',
        'event_end_date' => 'required|date_format:Y-m-d',
        'event_end_time' => 'required|date_format:H:i',
        'registration_deadline' => 'nullable|date',
    ]);

    // Preparando dados para inserção na tabela wp_posts
    $post_title = $request->post_title;
    $post_name = Str::slug($post_title);

    // Verifica se já existe um post com o mesmo post_name
    $existingPost = DB::table('wp_posts')->where('post_name', $post_name)->first();
    if ($existingPost) {
        $post_name .= '-'. (DB::table('wp_posts')->where('post_name', 'like', "$post_name%")->count() + 1);
    }

    $post_date = Carbon::now();

    $wpPostData = [
        'post_author' => 2,
        'post_date' => $post_date,
        'post_date_gmt' => $post_date->copy()->setTimezone('GMT'),
        'post_content' => $request->post_content,
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

    // Inserindo na tabela wp_posts e obtendo o ID inserido
    $postId = DB::table('wp_posts')->insertGetId($wpPostData);

    // Atualizando o campo guid com o ID do post
    DB::table('wp_posts')->where('ID', $postId)->update(['guid' => url("/event_listing?p=$postId")]);

    // Preparando dados para inserção na tabela wp_postmeta
    $metaData = [
        ['post_id' => $postId, 'meta_key' => '_featured', 'meta_value' => '0'],
        ['post_id' => $postId, 'meta_key' => '_edit_lock', 'meta_value' => '1720293949:2'],
        ['post_id' => $postId, 'meta_key' => '_edit_last', 'meta_value' => '2'],
        ['post_id' => $postId, 'meta_key' => '_view_count', 'meta_value' => '1'],
        ['post_id' => $postId, 'meta_key' => '_event_expiry_date', 'meta_value' => ''],
        ['post_id' => $postId, 'meta_key' => '_event_title', 'meta_value' => $post_title],
        ['post_id' => $postId, 'meta_key' => '_event_online', 'meta_value' => $request->event_online],
        ['post_id' => $postId, 'meta_key' => '_event_pincode', 'meta_value' => $request->event_pincode],
        ['post_id' => $postId, 'meta_key' => '_event_location', 'meta_value' => $request->event_location],
        ['post_id' => $postId, 'meta_key' => '_event_country', 'meta_value' => $request->event_country],
        ['post_id' => $postId, 'meta_key' => '_event_banner', 'meta_value' => $request->file('event_banner')->store('uploads', 'public')],
        ['post_id' => $postId, 'meta_key' => '_thumbnail_id', 'meta_value' => '3774'],
        ['post_id' => $postId, 'meta_key' => '_registration', 'meta_value' => $request->registration_email_url],
        ['post_id' => $postId, 'meta_key' => '_event_video_url', 'meta_value' => $request->video_url ?? ''],
        ['post_id' => $postId, 'meta_key' => '_event_start_date', 'meta_value' => $request->event_start_date . ' ' . $request->event_start_time . ':00'],
        ['post_id' => $postId, 'meta_key' => '_event_start_time', 'meta_value' => $request->event_start_time],
        ['post_id' => $postId, 'meta_key' => '_event_end_date', 'meta_value' => $request->event_end_date . ' ' . $request->event_end_time . ':00'],
        ['post_id' => $postId, 'meta_key' => '_event_end_time', 'meta_value' => $request->event_end_time],
        ['post_id' => $postId, 'meta_key' => '_event_registration_deadline', 'meta_value' => $request->registration_deadline],
        ['post_id' => $postId, 'meta_key' => '_event_venue_ids', 'meta_value' => ''],
        ['post_id' => $postId, 'meta_key' => '_juiz_principal', 'meta_value' => $request->juiz_principal],
        ['post_id' => $postId, 'meta_key' => '_juiz_linha1', 'meta_value' => $request->juiz_linha1],
        ['post_id' => $postId, 'meta_key' => '_juiz_linha2', 'meta_value' => $request->juiz_linha2],
    ];

    // Inserindo os metadados na tabela wp_postmeta
    DB::table('wp_postmeta')->insert($metaData);

    // Preparando dados para inserção na tabela wp_term_relationships
    $termRelationships = [
        ['object_id' => $postId, 'term_taxonomy_id' => $request->event_type, 'term_order' => 0],
        ['object_id' => $postId, 'term_taxonomy_id' => $request->event_category, 'term_order' => 0],
    ];

    // Inserindo os dados na tabela wp_term_relationships
    DB::table('wp_term_relationships')->insert($termRelationships);

    // Redirecionando para a página de listagem de jogos com uma mensagem de sucesso
    return redirect()->route('jogos.index')->with('success', 'Jogo adicionado com sucesso!');
}

public function edit($id)
{
    $jogo = WpPosts::with(['eventTypes.term', 'eventCategories.term', 'meta'])->findOrFail($id);
    $eventTypes = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_type')->get();
    $eventCategories = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_category')->get();
    $juizes = \App\Models\User::where('is_arbitro', true)->get();

    // Recupera os IDs dos juízes do jogo
    $juizPrincipalId = $jogo->getMetaValue('_juiz_principal');
    $juizLinha1Id = $jogo->getMetaValue('_juiz_linha1');
    $juizLinha2Id = $jogo->getMetaValue('_juiz_linha2');

    // dd($juizLinha1Id); die;

    // Recupera os detalhes dos juízes
    $juizPrincipal = $juizes->find($juizPrincipalId);
    $juizLinha1 = $juizes->find($juizLinha1Id);
    $juizLinha2 = $juizes->find($juizLinha2Id);

    return view('jogos.edit', compact('jogo', 'eventTypes', 'eventCategories', 'juizes', 'juizPrincipal', 'juizLinha1', 'juizLinha2'));
}



public function update(Request $request, $id)
{

     // Validação dos dados
     $request->validate([
        'post_title' => 'required|string|max:255',
        'event_type' => 'required|exists:wp_term_taxonomy,term_id',
        'event_category' => 'required|exists:wp_term_taxonomy,term_id',
        'event_online' => 'required|in:yes,no',
        'event_pincode' => 'required_if:event_online,no|string|max:10',
        'event_location' => 'required_if:event_online,no|string|max:255',
        'event_country' => 'required_if:event_online,no|string|max:2',
        'event_banner' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'post_content' => 'required|string',
        'registration_email_url' => 'required|string',
        'video_url' => 'nullable|string',
        'event_start_date' => 'required|date_format:Y-m-d',
        'event_start_time' => 'required|date_format:H:i',
        'event_end_date' => 'required|date_format:Y-m-d',
        'event_end_time' => 'required|date_format:H:i',
        'registration_deadline' => 'nullable|date_format:Y-m-d',
        'juiz_principal' => 'nullable|exists:users,id',
        'juiz_linha1' => 'nullable|exists:users,id',
        'juiz_linha2' => 'nullable|exists:users,id',
    ]);

    $jogo = WpPosts::findOrFail($id);
    
    // Atualizando wp_posts
    $jogo->update([
        'post_title' => $request->post_title,
        'post_content' => $request->post_content,
    ]);

    // Atualizando wp_postmeta
    $meta_fields = [
        '_event_title' => $request->post_title,
        '_event_online' => $request->event_online,
        '_event_pincode' => $request->event_pincode,
        '_event_location' => $request->event_location,
        '_event_country' => $request->event_country,
        '_registration' => $request->registration_email_url,
        '_event_video_url' => $request->video_url,
        '_event_start_date' => $request->event_start_date . ' ' . $request->event_start_time . ':00',
        '_event_end_date' => $request->event_end_date . ' ' . $request->event_end_time . ':00',
        '_event_start_time' => $request->event_start_time,
        '_event_end_time' => $request->event_end_time,
        '_event_registration_deadline' => $request->registration_deadline,
        '_juiz_principal' => $request->juiz_principal,
        '_juiz_linha1' => $request->juiz_linha1,
        '_juiz_linha2' => $request->juiz_linha2,
    ];

    foreach ($meta_fields as $key => $value) {
        $jogo->meta()->updateOrCreate(
            ['meta_key' => $key],
            ['meta_value' => $value]
        );
    }

    // Upload do banner do evento
    if ($request->hasFile('event_banner')) {
        $path = $request->file('event_banner')->store('banners', 'public');
        $jogo->meta()->updateOrCreate(
            ['meta_key' => '_event_banner'],
            ['meta_value' => $path]
        );
    }

    // Atualizando wp_term_relationships

    $jogo->eventTypes()->syncWithoutDetaching([$request->event_type]);
    $jogo->eventCategories()->syncWithoutDetaching([$request->event_category]);
    

    return redirect()->route('jogos.index')->with('success', 'Jogo atualizado com sucesso!');
}


    public function destroy($id)
    {
        $post = WpPosts::findOrFail($id);
        $post->delete();

        WpPostmeta::where('post_id', $id)->delete();

        return redirect()->route('jogos.index')->with('success', 'Jogo deletado com sucesso');
    }

    public function show()
    {
        return view('jogos.import');
    }


    public function import(Request $request)
    {
        // Validação do arquivo
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt',
        ]);

        // Caminho do arquivo
        $path = $request->file('csv_file')->getRealPath();
        $reader = Reader::createFromPath($path, 'r');
        $reader->setDelimiter(';'); // Ajuste o delimitador se necessário
        $reader->setHeaderOffset(0);
        $records = $reader->getRecords();
        $expectedColumns = [
            'titulo_evento', 'id_event', 'tipo_evento', 'ID_CATEGORY', 'categoria_evento', 'evento_on-line', 'CEP', 'local_evento', 'pais_evento',
            'descricao', 'email_url_registro', 'video_url', 'data_inicio', 'inicio', 'data_encerramento', 'encerramento', 'prazo_registro', 'ID_USERS',
            'juiz_principal', 'ID_USERS_1', 'juiz_linha1', 'ID_USERS_2', 'juiz_linha2'
        ];

        // Iterar sobre os registros e filtrar linhas vazias
        foreach ($records as $index => $record) {
            // Verificar se o registro contém informações além de apenas delimitadores
            if (isset($record['titulo_evento']) && !empty(trim($record['titulo_evento']))) {

                // Verificar se o número de colunas corresponde ao esperado
                if (count($record) !== count($expectedColumns)) {
                    return back()->withErrors(['error' => "Erro na linha $index: número de colunas incorreto."]);
                }

                // Convertendo os dados para UTF-8
                foreach ($record as $key => $value) {
                    $record[$key] = utf8_encode(trim($value));
                }

                try {
                    // Preparando dados para inserção na tabela wp_posts
                    $post_title = $record['titulo_evento'];
                    $post_name = Str::slug($post_title);

                    // Verifica se já existe um post com o mesmo post_name
                    $existingPost = DB::table('wp_posts')->where('post_name', $post_name)->first();
                    if ($existingPost) {
                        $post_name .= '-'. (DB::table('wp_posts')->where('post_name', 'like', "$post_name%")->count() + 1);
                    }

                    $post_date = Carbon::now();

                    $wpPostData = [
                        'post_author' => 2,
                        'post_date' => $post_date,
                        'post_date_gmt' => $post_date->copy()->setTimezone('GMT'),
                        'post_content' => $record['descricao'],
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

                    // Inserindo na tabela wp_posts e obtendo o ID inserido
                    $postId = DB::table('wp_posts')->insertGetId($wpPostData);

                    // Atualizando o campo guid com o ID do post
                    DB::table('wp_posts')->where('ID', $postId)->update(['guid' => url("/event_listing?p=$postId")]);

                    // Preparando dados para inserção na tabela wp_postmeta
                    $metaData = [
                        ['post_id' => $postId, 'meta_key' => '_featured', 'meta_value' => '0'],
                        ['post_id' => $postId, 'meta_key' => '_edit_lock', 'meta_value' => '1720293949:2'],
                        ['post_id' => $postId, 'meta_key' => '_edit_last', 'meta_value' => '2'],
                        ['post_id' => $postId, 'meta_key' => '_view_count', 'meta_value' => '1'],
                        ['post_id' => $postId, 'meta_key' => '_event_expiry_date', 'meta_value' => ''],
                        ['post_id' => $postId, 'meta_key' => '_event_title', 'meta_value' => $post_title],
                        ['post_id' => $postId, 'meta_key' => '_event_online', 'meta_value' => $record['evento_on-line']],
                        ['post_id' => $postId, 'meta_key' => '_event_pincode', 'meta_value' => $record['CEP']],
                        ['post_id' => $postId, 'meta_key' => '_event_location', 'meta_value' => $record['local_evento']],
                        ['post_id' => $postId, 'meta_key' => '_event_country', 'meta_value' => $record['pais_evento']],
                        ['post_id' => $postId, 'meta_key' => '_registration', 'meta_value' => $record['email_url_registro']],
                        ['post_id' => $postId, 'meta_key' => '_event_video_url', 'meta_value' => $record['video_url'] ?? ''],
                        ['post_id' => $postId, 'meta_key' => '_event_start_date', 'meta_value' => Carbon::createFromFormat('d/m/Y H:i:s', $record['data_inicio'] . ' ' . $record['inicio'])->format('Y-m-d H:i:s')],
                        ['post_id' => $postId, 'meta_key' => '_event_start_time', 'meta_value' => $record['inicio']],
                        ['post_id' => $postId, 'meta_key' => '_event_end_date', 'meta_value' => Carbon::createFromFormat('d/m/Y H:i:s', $record['data_encerramento'] . ' ' . $record['encerramento'])->format('Y-m-d H:i:s')],
                        ['post_id' => $postId, 'meta_key' => '_event_end_time', 'meta_value' => $record['encerramento']],
                        ['post_id' => $postId, 'meta_key' => '_event_registration_deadline', 'meta_value' => $record['prazo_registro']],
                        ['post_id' => $postId, 'meta_key' => '_event_venue_ids', 'meta_value' => ''],
                        ['post_id' => $postId, 'meta_key' => '_juiz_principal', 'meta_value' => $record['ID_USERS']],
                        ['post_id' => $postId, 'meta_key' => '_juiz_linha1', 'meta_value' => $record['ID_USERS_1']],
                        ['post_id' => $postId, 'meta_key' => '_juiz_linha2', 'meta_value' => $record['ID_USERS_2']],
                    ];

                    // Inserindo os metadados na tabela wp_postmeta
                    DB::table('wp_postmeta')->insert($metaData);

                    // Preparando dados para inserção na tabela wp_term_relationships
                    $termRelationships = [
                        ['object_id' => $postId, 'term_taxonomy_id' => $record['id_event'], 'term_order' => 0],
                        ['object_id' => $postId, 'term_taxonomy_id' => $record['ID_CATEGORY'], 'term_order' => 0],
                    ];

                    // Inserindo os dados na tabela wp_term_relationships
                    DB::table('wp_term_relationships')->insert($termRelationships);
                } catch (Exception $e) {
                    // Log de erro ou tratamento de exceção aqui
                    return back()->withErrors(['error' => $e->getMessage()]);
                }
            }
        }

        return view('jogos.index')->with('success', 'Jogos importados com sucesso.');
    }


     /**
     * Projeto Futuro: Inserir mais de um jogo no mesmo submit
     */

    /*
    public function createMulti()
    {

        $juizes = \App\Models\User::where('is_arbitro', true)->get();

        $eventTypes = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_type')->get();

        $eventCategorys = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_category')->get();

        return view('jogos.multi-insert', compact('eventTypes', 'eventCategorys', 'juizes'));
        
    }
    */


}
