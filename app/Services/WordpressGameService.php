<?php

namespace App\Services;

use App\Models\Jogo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class WordpressGameService
{
    /**
     * Sincroniza um jogo local com as tabelas do WordPress.
     *
     * @param Jogo $jogo
     * @param array $extraData (dados adicionais como n_jogo, tipo_evento_id, categoria_evento_id)
     * @param int|null $wpPostId ID do post WP se for atualização
     * @return int ID do Post criado/atualizado
     */
    public function sync(Jogo $jogo, array $extraData, $wpPostId = null)
    {
        // Carrega relações necessárias
        $jogo->load(['mandante.equipe', 'visitante.equipe', 'ginasio']);

        // 1. Gera o Título
        $nomeMandante = $jogo->mandante->equipe->eqp_nome_detalhado ?? 'Mandante';
        $nomeVisitante = $jogo->visitante->equipe->eqp_nome_detalhado ?? 'Visitante';
        $postTitle = "{$nomeMandante} X {$nomeVisitante}";

        // 2. Gera o Local Formatado
        $local = "Local Indefinido";
        if ($jogo->ginasio) {
            $gin = $jogo->ginasio;
            $endereco = $gin->gin_endereco;
            if ($gin->gin_numero) $endereco .= ", " . $gin->gin_numero;
            if ($gin->gin_bairro) $endereco .= " - " . $gin->gin_bairro;
            $local = "{$gin->gin_nome} - {$endereco}";
        }

        // 3. Prepara dados do WP_POST
        $postName = Str::slug($postTitle);
        $postDate = Carbon::now();

        // Verifica unicidade do slug se for criar
        if (!$wpPostId) {
            $existingPost = DB::table('wp_posts')->where('post_name', $postName)->first();
            if ($existingPost) {
                // Adiciona contador para evitar colisão
                $count = DB::table('wp_posts')->where('post_name', 'like', "$postName%")->count();
                $postName .= '-' . ($count + 1);
            }
        }

        $wpPostData = [
            'post_title' => $postTitle,
            'post_content' => $postTitle, // Conteúdo igual ao título conforme solicitado
            'post_excerpt' => '', // Campo obrigatório
            'to_ping' => '', // Campo obrigatório
            'pinged' => '', // Campo obrigatório
            'post_content_filtered' => '', // Possivelmente obrigatório
            'post_name' => $postName,
            'post_modified' => $postDate,
            'post_modified_gmt' => $postDate->copy()->setTimezone('GMT'),
        ];

        if ($wpPostId) {
            // Update
            DB::table('wp_posts')->where('ID', $wpPostId)->update($wpPostData);
        } else {
            // Create
            $wpPostData['post_author'] = 2; // Mantendo hardcoded conforme original
            $wpPostData['post_date'] = $postDate;
            $wpPostData['post_date_gmt'] = $postDate->copy()->setTimezone('GMT');
            $wpPostData['post_status'] = 'publish';
            $wpPostData['comment_status'] = 'closed';
            $wpPostData['ping_status'] = 'closed';
            $wpPostData['post_type'] = 'event_listing';
            
            $wpPostId = DB::table('wp_posts')->insertGetId($wpPostData);
            
            // Atualiza GUID
            DB::table('wp_posts')->where('ID', $wpPostId)->update(['guid' => "https://lrvoleibol.com.br/event_listing?p=$wpPostId"]);
        }

        // 4. Prepara MetaDados
        $startDateTime = new \DateTime($jogo->jgo_dt_jogo . ' ' . $jogo->jgo_hora_jogo);
        $endDateTime = clone $startDateTime;
        $endDateTime->modify('+2 hours 30 minutes');

        $metaData = [
            '_event_number' => $extraData['event_number'],
            '_event_title' => $postTitle,
            '_event_location' => $local,
            '_event_country' => 'Brasil',
            '_event_start_date' => $startDateTime->format('Y-m-d H:i:s'),
            '_event_start_time' => $startDateTime->format('H:i:s'),
            '_event_end_date' => $endDateTime->format('Y-m-d H:i:s'),
            '_event_end_time' => $endDateTime->format('H:i:s'),
            '_juiz_principal' => $jogo->jgo_arbitro_principal,
            '_juiz_linha1' => $jogo->jgo_arbitro_secundario,
            '_juiz_linha2' => $jogo->jgo_apontador,
            
            // Campos padrão mantidos do original
            '_featured' => '0',
            '_edit_lock' => '1720293949:2', 
            '_edit_last' => '2',
            '_view_count' => '1',
            '_event_online' => 'no',
            '_thumbnail_id' => '4132',
            '_event_banner' => 'https://lrvoleibol.com.br/wp-content/uploads/2024/07/voleibol.jpg',
            '_cancelled' => '0',
            '_registration' => Auth::user()->email ?? 'admin@lrvoleibol.com.br',
            
            // Campos de persistência local linkando à tabela jogos
            '_mandante_id' => $jogo->jgo_eqp_cpo_mandante_id,
            '_visitante_id' => $jogo->jgo_eqp_cpo_visitante_id,
            '_ginasio_id' => $jogo->jgo_local_jogo_id,
            '_local_jogo_id' => $jogo->jgo_id,
        ];

        // Atualiza ou Insere Metadados
        foreach ($metaData as $key => $value) {
            DB::table('wp_postmeta')->updateOrInsert(
                ['post_id' => $wpPostId, 'meta_key' => $key],
                ['meta_value' => $value]
            );
        }

        // 5. Relacionamentos de Termos (Taxonomias)
        // Limpa anteriores se update
        DB::table('wp_term_relationships')->where('object_id', $wpPostId)->delete();
        
        $termRelationships = [];
        if (isset($extraData['event_type'])) {
            $termRelationships[] = ['object_id' => $wpPostId, 'term_taxonomy_id' => $extraData['event_type'], 'term_order' => 0];
        }
        if (isset($extraData['event_category'])) {
            $termRelationships[] = ['object_id' => $wpPostId, 'term_taxonomy_id' => $extraData['event_category'], 'term_order' => 0];
        }

        if (!empty($termRelationships)) {
            DB::table('wp_term_relationships')->insert($termRelationships);
        }

        // 6. Hook externo para thumbnail (mantido do original)
        $this->updateThumbnail($wpPostId);

        return $wpPostId;
    }

    private function updateThumbnail($postId)
    {
        try {
            $domain = (app()->environment('local') || str_contains(request()->getHost(), 'develop')) 
                        ? 'http://lrvoleibol.develop' 
                        : 'https://lrvoleibol.com.br';

            $response = Http::post("{$domain}/wp-json/custom/v1/update_event_thumbnail", [
                'post_id'       => $postId,
                'attachment_id' => 4132
            ]);
            
            if (!$response->successful()) {
                Log::error('Erro ao destacar imagem do evento WP', ['post_id' => $postId, 'body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao chamar API de thumbnail WP: ' . $e->getMessage());
        }
    }
}
