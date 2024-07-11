<?php

namespace App\Http\Controllers;

use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use App\Models\Wp_Term_Taxonomy;
use App\Models\Wp_Terms;
use App\Models\WpPosts;
use App\Models\WpPostmeta;
use App\Models\Wp_Term_Relationships;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

    public function create()
    {
        $eventTypes = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_type')->get();

        $eventCategorys = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_category')->get();

        return view('jogos.create', compact('eventTypes', 'eventCategorys'));
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
        'event_start_date' => 'required|date',
        'event_start_time' => 'required|date_format:H:i',
        'event_end_date' => 'required|date',
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
        ['post_id' => $postId, 'meta_key' => '_event_start_date', 'meta_value' => $request->event_start_date . ' ' . $request->event_start_time],
        ['post_id' => $postId, 'meta_key' => '_event_start_time', 'meta_value' => $request->event_start_time],
        ['post_id' => $postId, 'meta_key' => '_event_end_date', 'meta_value' => $request->event_end_date . ' ' . $request->event_end_time],
        ['post_id' => $postId, 'meta_key' => '_event_end_time', 'meta_value' => $request->event_end_time],
        ['post_id' => $postId, 'meta_key' => '_event_registration_deadline', 'meta_value' => $request->registration_deadline],
        ['post_id' => $postId, 'meta_key' => '_event_venue_ids', 'meta_value' => ''],
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
        $jogo = WpPosts::findOrFail($id);
        $tipos = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_type')->get();
        $categorias = Wp_Term_Taxonomy::with('term')->where('taxonomy', 'event_listing_category')->get();
        return view('jogos.edit', compact('jogo', 'tipos', 'categorias'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'tipo' => 'required|integer',
            'categoria' => 'required|integer',
            'evento_online' => 'required|boolean',
            'cep' => 'required|string|max:10',
            'local' => 'required|string|max:255',
            'pais' => 'required|string|max:255',
            'banner' => 'nullable|image|mimes:jpeg,png|max:2048',
            'descricao' => 'required|string',
            'email_url' => 'required|string|max:255',
            'video_url' => 'nullable|string|max:255',
            'data_inicio' => 'required|date',
            'hora_inicio' => 'required|date_format:H:i',
            'data_encerramento' => 'required|date',
            'hora_encerramento' => 'required|date_format:H:i',
            'prazo_registro' => 'nullable|date',
        ]);

        $post = WpPosts::findOrFail($id);
        $post->update([
            'post_title' => $request->input('title'),
            'post_content' => $request->input('descricao'),
        ]);

        // Upload do banner
        if ($request->hasFile('banner')) {
            $bannerPath = $request->file('banner')->store('banners', 'public');
            WpPostmeta::updateOrCreate(
                ['post_id' => $post->ID, 'meta_key' => '_banner'],
                ['meta_value' => $bannerPath]
            );
        }

        // Atualizando os metadados do evento
        $metaData = [
            '_tipo' => $request->input('tipo'),
            '_categoria' => $request->input('categoria'),
            '_evento_online' => $request->input('evento_online'),
            '_cep' => $request->input('cep'),
            '_local' => $request->input('local'),
            '_pais' => $request->input('pais'),
            '_email_url' => $request->input('email_url'),
            '_video_url' => $request->input('video_url'),
            '_data_inicio' => $request->input('data_inicio'),
            '_hora_inicio' => $request->input('hora_inicio'),
            '_data_encerramento' => $request->input('data_encerramento'),
            '_hora_encerramento' => $request->input('hora_encerramento'),
            '_prazo_registro' => $request->input('prazo_registro'),
        ];

        foreach ($metaData as $key => $value) {
            WpPostmeta::updateOrCreate(
                ['post_id' => $post->ID, 'meta_key' => $key],
                ['meta_value' => $value]
            );
        }

        return redirect()->route('jogos.index')->with('success', 'Jogo atualizado com sucesso');
    }

    public function destroy($id)
    {
        $post = WpPosts::findOrFail($id);
        $post->delete();

        WpPostmeta::where('post_id', $id)->delete();

        return redirect()->route('jogos.index')->with('success', 'Jogo deletado com sucesso');
    }
}
