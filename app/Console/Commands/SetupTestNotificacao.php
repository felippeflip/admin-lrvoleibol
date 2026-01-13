<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jogo;
use App\Models\User;
use App\Models\EquipeCampeonato;
use App\Models\Ginasio;
use Carbon\Carbon;

class SetupTestNotificacao extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:setup-notificacao {email? : Email do usuário para ser o apontador}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um jogo fictício finalizado há mais de 3 horas para testar a notificação de apontadores.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if ($email) {
            $user = User::where('email', $email)->first();
            if (!$user) {
                $this->error("Usuário com email {$email} não encontrado.");
                return;
            }
        } else {
            // Pega o primeiro usuário admin ou qualquer um
            $user = User::first();
            if (!$user) {
                $this->error("Nenhum usuário encontrado na base de dados.");
                return;
            }
        }

        $mandante = EquipeCampeonato::inRandomOrder()->first();
        $visitante = EquipeCampeonato::where('eqp_cpo_id', '!=', $mandante->eqp_cpo_id)->inRandomOrder()->first();
        $ginasio = Ginasio::first();

        if (!$mandante || !$visitante || !$ginasio) {
            $this->error("É necessário ter pelo menos 2 equipes em campeonatos e 1 ginásio cadastrado.");
            return;
        }

        // Simula um jogo que aconteceu 5 horas atrás
        $dataHoraJogo = Carbon::now()->subHours(5);

        $jogo = Jogo::create([
            'jgo_dt_jogo' => $dataHoraJogo->format('Y-m-d'),
            'jgo_hora_jogo' => $dataHoraJogo->format('H:i:s'),
            'jgo_local_jogo_id' => $ginasio->gin_id,
            'jgo_eqp_cpo_mandante_id' => $mandante->eqp_cpo_id,
            'jgo_eqp_cpo_visitante_id' => $visitante->eqp_cpo_id,
            'jgo_apontador' => $user->id,
            'jgo_status' => 'finalizado', // ou agendado, dependendo da lógica, mas o comando verifica apenas data
            'jgo_res_status' => null, // Resultado pendente
            'jgo_notificacao_resultado' => false,
        ]);

        $this->info("Jogo de teste criado com sucesso!");
        $this->table(
            ['ID', 'Data/Hora', 'Mandante', 'Visitante', 'Apontador', 'Email'],
            [[
                $jogo->jgo_id, 
                $dataHoraJogo->format('d/m/Y H:i'), 
                $mandante->equipe->eqp_nome ?? $mandante->eqp_cpo_id, 
                $visitante->equipe->eqp_nome ?? $visitante->eqp_cpo_id,
                $user->name,
                $user->email
            ]]
        );

        $this->info("\nAgora execute o comando de notificação para testar o envio:");
        $this->comment("php artisan notify:apontadores-resultados");
    }
}
