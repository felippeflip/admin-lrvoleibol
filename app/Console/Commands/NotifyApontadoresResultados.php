<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jogo;
use App\Mail\LembreteResultadoNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class NotifyApontadoresResultados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notify:apontadores-resultados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia e-mail de lembrete para apontadores inserirem o resultado de jogos finalizados há 3 horas.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Regra:
        // 1. Data/Hora do jogo + 3h < Agora
        // 2. Status != 'cancelado' (assumindo que existe tal status, ou apenas verificando se está ativo)
        // 3. Resultado não informado (jgo_res_status = null ou 'nao_informado')
        // 4. Notificação ainda não enviada (jgo_notificacao_resultado = false)
        // 5. Apontador definido

        $now = Carbon::now();
        $this->info("Iniciando verificação de jogos pendentes de resultado em {$now}...");

        $jogos = Jogo::where(function($q) {
                        $q->whereNull('jgo_res_status')
                          ->orWhere('jgo_res_status', 'nao_informado');
                    })
                    ->where('jgo_notificacao_resultado', false)
                    ->get();
        
        $count = 0;

        foreach ($jogos as $jogo) {
            // Calculate Game End Time Threshold (Start Time + 3 Hours)
            // Combine Date and Time
            try {
                $gameTime = Carbon::parse($jogo->jgo_dt_jogo . ' ' . $jogo->jgo_hora_jogo);
                $threshold = $gameTime->addHours(3);

                if ($now->greaterThan($threshold)) {
                    // Check if Apontador exists
                    if ($jogo->apontador) {
                        $this->info("Enviando lembrete para Jogo ID: {$jogo->jgo_id} - Apontador: {$jogo->apontador->name}");
                        
                        Mail::to($jogo->apontador->email)->send(new LembreteResultadoNotification($jogo, $jogo->apontador));
                        
                        $jogo->jgo_notificacao_resultado = true;
                        $jogo->saveQuietly();
                        
                        $count++;
                    } else {
                         //$this->warn("Jogo ID: {$jogo->jgo_id} estaria elegível, mas não tem apontador definido.");
                    }
                }
            } catch (\Exception $e) {
                Log::error("Erro ao processar notificação de jogo {$jogo->jgo_id}: " . $e->getMessage());
            }
        }

        $this->info("Processamento concluído. {$count} lembretes enviados.");
    }
}
