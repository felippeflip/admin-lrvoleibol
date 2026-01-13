<?php

namespace App\Observers;

use App\Models\Jogo;
use App\Mail\EscalaArbitragemNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class JogoObserver
{
    /**
     * Handle the Jogo "created" event.
     */
    public function created(Jogo $jogo): void
    {
        $this->checkAndSendNotifications($jogo);
    }

    /**
     * Handle the Jogo "updated" event.
     */
    public function updated(Jogo $jogo): void
    {
        $this->checkAndSendNotifications($jogo);
    }

    protected function checkAndSendNotifications(Jogo $jogo)
    {
        Log::info("JogoObserver: Verificando notificações para Jogo ID {$jogo->jgo_id}...");
        
        // 1. Arbitro Principal
        if ($jogo->jgo_arbitro_principal) {
            // Check if it's a new assignment (wasChanged) OR if it was never notified (e.g. initial import/create)
            // Note: On 'created', wasChanged returns true for set attributes.
            // However, we also check if the notification flag is false.
            // If the user CHANGED the arbiter, we should send.
            
            $shouldSend = $jogo->wasChanged('jgo_arbitro_principal') || !$jogo->jgo_notificacao_arbitro_p;

            if ($shouldSend) {
                try {
                    Mail::to($jogo->arbitroPrincipal->email)->send(new EscalaArbitragemNotification($jogo, $jogo->arbitroPrincipal, 'Árbitro Principal'));
                    
                    // Update flag quietly to avoid infinite loop
                    $jogo->jgo_notificacao_arbitro_p = true;
                    $jogo->saveQuietly();
                } catch (\Exception $e) {
                    Log::error("Falha ao enviar e-mail para Árbitro Principal (Jogo {$jogo->jgo_id}): " . $e->getMessage());
                }
            }
        }

        // 2. Arbitro Secundario
        if ($jogo->jgo_arbitro_secundario) {
            $shouldSend = $jogo->wasChanged('jgo_arbitro_secundario') || !$jogo->jgo_notificacao_arbitro_s;

            if ($shouldSend) {
                try {
                    Mail::to($jogo->arbitroSecundario->email)->send(new EscalaArbitragemNotification($jogo, $jogo->arbitroSecundario, 'Árbitro Secundário'));
                    
                    $jogo->jgo_notificacao_arbitro_s = true;
                    $jogo->saveQuietly();
                } catch (\Exception $e) {
                    Log::error("Falha ao enviar e-mail para Árbitro Secundário (Jogo {$jogo->jgo_id}): " . $e->getMessage());
                }
            }
        }

        // 3. Apontador
        if ($jogo->jgo_apontador) {
            $shouldSend = $jogo->wasChanged('jgo_apontador') || !$jogo->jgo_notificacao_apontador;

            if ($shouldSend) {
                try {
                    Mail::to($jogo->apontador->email)->send(new EscalaArbitragemNotification($jogo, $jogo->apontador, 'Apontador'));
                    
                    $jogo->jgo_notificacao_apontador = true;
                    $jogo->saveQuietly();
                } catch (\Exception $e) {
                    Log::error("Falha ao enviar e-mail para Apontador (Jogo {$jogo->jgo_id}): " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Handle the Jogo "deleted" event.
     */
    public function deleted(Jogo $jogo): void
    {
        //
    }
}
