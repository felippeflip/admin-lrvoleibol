<?php

namespace App\Services;

use App\Models\Jogo;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class WordpressGameService
{
    private $wpUrl;
    private $wpUsername;
    private $wpPassword;

    public function __construct()
    {
        $this->wpUrl = rtrim(env('WP_URL', 'https://lrvoleibol.com.br'), '/');
        $this->wpUsername = env('WP_USERNAME', '');
        // Suporta as duas chaves enviadas
        $this->wpPassword = env('WP_APPLICATION_PASSWORD', env('WP_APP_PASSWORD', ''));
    }

    /**
     * Sincroniza um jogo local com os CPTs do WordPress via API Rest.
     *
     * @param Jogo $jogo
     * @param array $extraData (dados adicionais como n_jogo, tipo_evento_id, categoria_evento_id)
     * @param int|null $wpPostId ID do post WP se for atualização
     * @return int|null ID do Post criado/atualizado
     */
    public function sync(Jogo $jogo, array $extraData, $wpPostId = null)
    {
        // Carrega relações necessárias para ter dados dos times
        $jogo->load(['mandante.equipe.time', 'visitante.equipe.time', 'ginasio']);

        // 1. Gera o Título
        $nomeMandante = $jogo->mandante->equipe->eqp_nome_detalhado ?? 'Mandante';
        $nomeVisitante = $jogo->visitante->equipe->eqp_nome_detalhado ?? 'Visitante';
        $postTitle = "{$nomeMandante} X {$nomeVisitante}";

        // 2. Gera o Local Formatado
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

        // 3. Mesclar as Imagens usando o GameImageService
        $gameImageService = new GameImageService();
        $mandanteLogo = $jogo->mandante->equipe->time->tim_logo ?? null;
        $visitanteLogo = $jogo->visitante->equipe->time->tim_logo ?? null;

        $localImagePath = $gameImageService->createGameImage($mandanteLogo, $visitanteLogo, $jogo->jgo_id);

        $wpMediaId = null;
        $wpMediaUrl = null;

        if ($localImagePath) {
            $absolutePath = Storage::disk('public')->path($localImagePath);
            $filename = basename($absolutePath);

            // Upload the image via REST API
            $mediaResponse = $this->uploadMediaToWordpress($absolutePath, $filename);
            if ($mediaResponse) {
                $wpMediaId = $mediaResponse['id'];
                $wpMediaUrl = $mediaResponse['source_url'] ?? null;
            }
        }

        // 4. Prepara MetaDados (+ Datas)
        // jgo_dt_jogo e jgo_hora_jogo podem chegar com formatos variados (datetime, date, string).
        // Extraímos apenas a parte necessária de cada um para evitar erro de especificação dupla de hora.
        $datePart      = Carbon::parse($jogo->jgo_dt_jogo)->format('Y-m-d');
        $timePart      = Carbon::parse($jogo->jgo_hora_jogo)->format('H:i:s');
        $startDateTime = new \DateTime($datePart . ' ' . $timePart);
        $endDateTime   = clone $startDateTime;
        $endDateTime->modify('+2 hours 30 minutes');

        // Note: Todos os campos custom fields foram validados no wordpress para aceitarem single strings
        $payload = [
            'status' => 'publish',
            'title' => $postTitle,
            'content' => $postTitle,
            'slug' => Str::slug($postTitle),
            'meta' => [
                '_event_number' => (string) $extraData['event_number'],
                '_event_title' => $postTitle,
                '_event_location' => $local,
                '_event_country' => 'Brasil',
                '_event_start_date' => $startDateTime->format('Y-m-d H:i:s'),
                '_event_start_time' => $startDateTime->format('H:i:s'),
                '_event_end_date' => $endDateTime->format('Y-m-d H:i:s'),
                '_event_end_time' => $endDateTime->format('H:i:s'),
                '_juiz_principal' => (string) $jogo->jgo_arbitro_principal,
                '_juiz_linha1' => (string) $jogo->jgo_arbitro_secundario,
                '_juiz_linha2' => (string) $jogo->jgo_apontador,

                '_featured' => '0',
                '_event_online' => 'no',
                '_cancelled' => '0',
                '_registration' => Auth::user()->email ?? 'admin@lrvoleibol.com.br',

                '_mandante_id' => (string) $jogo->jgo_eqp_cpo_mandante_id,
                '_visitante_id' => (string) $jogo->jgo_eqp_cpo_visitante_id,
                '_ginasio_id' => (string) $jogo->jgo_local_jogo_id,
                '_local_jogo_id' => (string) $jogo->jgo_id,
            ]
        ];

        // Lida com Categorias e Tipos
        if (isset($extraData['event_type'])) {
            $payload['event_listing_type'] = [(int) $extraData['event_type']];
        }
        if (isset($extraData['event_category'])) {
            $payload['event_listing_category'] = [(int) $extraData['event_category']];
        }

        // Atualização de Imagem
        if ($wpMediaId) {
            $payload['featured_media'] = $wpMediaId;
            $payload['meta']['_thumbnail_id'] = (string) $wpMediaId;
            if ($wpMediaUrl) {
                // Caso use outra chave para o banner, atrelar a mesma url
                $payload['meta']['_event_banner'] = $wpMediaUrl;
            }
        }

        // 5. Enviar POST/PUT para a REST API
        try {
            if ($wpPostId) {
                // Update post
                $response = Http::withBasicAuth($this->wpUsername, $this->wpPassword)
                    ->post("{$this->wpUrl}/wp-json/wp/v2/event_listing/{$wpPostId}", $payload);
            } else {
                // Create Post
                $response = Http::withBasicAuth($this->wpUsername, $this->wpPassword)
                    ->post("{$this->wpUrl}/wp-json/wp/v2/event_listing", $payload);
            }

            if ($response->successful()) {
                $createdPostId = $response->json('id');

                // Tratar term_relationships via API separada se não suportado diretamente pelo endpoint de posts
                // Algumas APIs REST mapeiam taxomomies se enviarmos os arrays de IDs baseados na configuração (rest_base)
                // Ex: "event_listing_category" => [$extraData['event_category']]
                // Mas, o jeito legado do sistema mexia na tabela. Tentaremos chamar custom hook original para não perder info se não suportado.

                return $createdPostId;
            } else {
                Log::error('Erro ao conectar via WP REST API no jogo', [
                    'body' => $response->body(),
                    'status' => $response->status()
                ]);
                return $wpPostId;
            }

        } catch (\Exception $e) {
            Log::error('Exceção ao ligar com WP REST API: ' . $e->getMessage());
            return $wpPostId;
        }
    }

    /**
     * Deleta um jogo no WordPress via API Rest.
     *
     * @param int $wpPostId ID do post WP
     * @return bool
     */
    public function delete($wpPostId)
    {
        try {
            // Primeiro, tenta o endpoint customizado para limpar também imagens e cache
            $customResponse = Http::withBasicAuth($this->wpUsername, $this->wpPassword)
                ->post("{$this->wpUrl}/wp-json/custom/v1/delete_event", [
                    'post_id' => $wpPostId
                ]);

            if ($customResponse->successful()) {
                Log::info("Jogo {$wpPostId} deletado via endpoint customizado do WP.");
                return true;
            }

            // Se falhar o customizado (ou não existir), chama o padrao do REST API
            Log::warning("Endpoint customizado falhou, tentando rota padrão do WP REST API para {$wpPostId}.");
            $response = Http::withBasicAuth($this->wpUsername, $this->wpPassword)
                ->delete("{$this->wpUrl}/wp-json/wp/v2/event_listing/{$wpPostId}?force=true");

            if ($response->successful()) {
                return true;
            } else {
                Log::error('Erro ao deletar jogo via WP REST API', [
                    'body' => $response->body(),
                    'status' => $response->status()
                ]);
                return false;
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao deletar jogo no WP API: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Faz upload da imagem ao endpoint /wp/v2/media.
     */
    private function uploadMediaToWordpress($localPath, $filename)
    {
        try {
            $fileData = file_get_contents($localPath);
            $mimeType = mime_content_type($localPath);

            $response = Http::withBasicAuth($this->wpUsername, $this->wpPassword)
                ->withHeaders([
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                    'Content-Type' => $mimeType,
                ])
                ->withBody($fileData, $mimeType)
                ->post("{$this->wpUrl}/wp-json/wp/v2/media");

            if ($response->successful()) {
                return $response->json(); // Array com 'id', 'source_url', etc.
            } else {
                Log::error('Upload REST API do WP falhou: ' . $response->body());
                return null;
            }
        } catch (\Exception $e) {
            Log::error('Exceção ao fazer upload ao WP: ' . $e->getMessage());
            return null;
        }
    }
}
