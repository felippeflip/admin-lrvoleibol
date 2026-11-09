<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jogo;
use App\Services\WordpressGameService;
use Illuminate\Support\Facades\DB;

class RepararJogosWordpress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jogos:reparar-wp 
                            {--jogo= : ID específico de um jogo para sincronizar}
                            {--force : Executa sem solicitar confirmação}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Identifica jogos aprovados com data/hora/local que não estão no WordPress (ou com ID corrompido) e sincroniza-os com a API do site.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info("================================================================");
        $this->info("    SINCRONIZADOR / REPARADOR DE JOGOS NO WORDPRESS");
        $this->info("================================================================");

        $especificoId = $this->option('jogo');

        // Busca jogos com agendamento completo (data, hora e local)
        $query = Jogo::with([
            'mandante.campeonato',
            'mandante.equipe.categoria',
            'mandante.equipe.time',
            'visitante.equipe.time',
            'ginasio'
        ])
        ->whereNotNull('jgo_dt_jogo')
        ->whereNotNull('jgo_hora_jogo')
        ->whereNotNull('jgo_local_jogo_id');

        if ($especificoId) {
            $query->where('jgo_id', $especificoId);
        } else {
            // Jogos que têm status aprovado, mas jgo_wp_id é NULL ou igual ao próprio jgo_id (corrompido)
            $query->where(function ($q) {
                $q->whereNull('jgo_wp_id')
                  ->orWhereColumn('jgo_wp_id', 'jgo_id');
            });
        }

        $jogos = $query->orderBy('jgo_id')->get();

        if ($jogos->isEmpty()) {
            $this->info("Nenhum jogo pendente de sincronização ou reparo encontrado.");
            return self::SUCCESS;
        }

        $this->warn("Foram encontrados {$jogos->count()} jogo(s) para sincronização.");

        if (!$this->option('force') && !$this->confirm("Deseja sincronizar estes {$jogos->count()} jogo(s) agora com o WordPress?")) {
            $this->info("Operação cancelada pelo usuário.");
            return self::SUCCESS;
        }

        $wpService = new WordpressGameService();
        $sucessos = 0;
        $falhas = 0;
        $tabelaRelatorio = [];

        $progressBar = $this->output->createProgressBar($jogos->count());
        $progressBar->start();

        foreach ($jogos as $jogo) {
            $nomeMandante = $jogo->mandante->equipe->time->tim_nome ?? 'Mandante';
            $nomeVisitante = $jogo->visitante->equipe->time->tim_nome ?? 'Visitante';
            $numJogo = $jogo->jgo_numero_jogo ?: $jogo->jgo_id;

            $eventType = $jogo->mandante->campeonato->cpo_term_tx_id ?? null;
            $eventCategory = $jogo->mandante->equipe->categoria->cto_term_tx_id ?? null;

            // Se o wp_id é igual ao ID local, é falso/corrompido, logo passa null para criar novo
            $realWpId = ($jogo->jgo_wp_id && (int)$jogo->jgo_wp_id !== (int)$jogo->jgo_id) ? $jogo->jgo_wp_id : null;

            try {
                $wpPostId = $wpService->sync($jogo, [
                    'event_number'   => $numJogo,
                    'event_type'     => $eventType,
                    'event_category' => $eventCategory,
                ], $realWpId);

                if ($wpPostId) {
                    $jogo->update([
                        'jgo_wp_id' => $wpPostId,
                        'jgo_status_agendamento' => 'aprovado'
                    ]);
                    $sucessos++;
                    $tabelaRelatorio[] = [
                        $jogo->jgo_id,
                        "#{$numJogo}",
                        "{$nomeMandante} X {$nomeVisitante}",
                        $jogo->fase_completa,
                        $wpPostId,
                        '<fg=green>Sincronizado</>'
                    ];
                } else {
                    $falhas++;
                    $tabelaRelatorio[] = [
                        $jogo->jgo_id,
                        "#{$numJogo}",
                        "{$nomeMandante} X {$nomeVisitante}",
                        $jogo->fase_completa,
                        '-',
                        '<fg=red>Erro na API WP</>'
                    ];
                }
            } catch (\Exception $e) {
                $falhas++;
                $tabelaRelatorio[] = [
                    $jogo->jgo_id,
                    "#{$numJogo}",
                    "{$nomeMandante} X {$nomeVisitante}",
                    $jogo->fase_completa,
                    '-',
                    '<fg=red>' . substr($e->getMessage(), 0, 30) . '</>'
                ];
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->table(
            ['ID Local', 'Nº Jogo', 'Confronto', 'Fase', 'WP Post ID', 'Status'],
            $tabelaRelatorio
        );

        $this->info("================================================================");
        $this->info(" Concluído! Sucessos: {$sucessos} | Falhas: {$falhas}");
        $this->info("================================================================");

        return $falhas === 0 ? self::SUCCESS : self::FAILURE;
    }
}

