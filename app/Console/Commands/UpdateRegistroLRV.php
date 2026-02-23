<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Atleta;
use App\Models\User;

class UpdateRegistroLRV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lrv:update-registros';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Atualiza o número de Registro LRV para Atletas e Árbitros, buscando o próximo número da tabela de sequências baseando-se na data de nascimento do mais velho para o mais novo.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando atualização de Registros LRV...');

        $this->updateAtletas();
        $this->updateArbitros();

        $this->info('Atualização concluída com sucesso!');
    }

    private function updateAtletas()
    {
        $this->info('Processando Atletas...');

        // Buscar atletas sem registro LRV (atl_resg) ordenados por data de nascimento (mais velhos primeiro)
        // Ignorar quem não tem data de nascimento cadastrada, pois a regra exige isso.
        $atletas = Atleta::whereNull('atl_resg')
            ->whereNotNull('atl_dt_nasc')
            ->orderBy('atl_dt_nasc', 'asc')
            ->get();

        if ($atletas->isEmpty()) {
            $this->info('Nenhum Atleta para atualizar.');
            return;
        }

        $sequence = DB::table('atleta_sequences')->first();
        if (!$sequence) {
            $this->error('Tabela atleta_sequences não inicializada.');
            return;
        }

        $nextNumber = $sequence->next_number;
        $count = 0;

        foreach ($atletas as $atleta) {
            /** @var Atleta $atleta */
            $atleta->atl_resg = $nextNumber;
            $atleta->save();

            $nextNumber++;
            $count++;
        }

        // Atualiza a sequence para o próximo número disponível
        DB::table('atleta_sequences')->update(['next_number' => $nextNumber, 'updated_at' => now()]);

        $this->info("{$count} Atletas atualizados com sucesso!");
    }

    private function updateArbitros()
    {
        $this->info('Processando Árbitros/Apontadores (Juízes)...');

        // Buscar usuários com perfil "Juiz" sem registro LRV (lrv) ordenados por data de nascimento
        $arbitros = User::whereHas('roles', function ($query) {
            $query->where('name', 'Juiz');
        })
            ->whereNull('lrv')
            ->whereNotNull('data_nascimento')
            ->orderBy('data_nascimento', 'asc')
            ->get();

        if ($arbitros->isEmpty()) {
            $this->info('Nenhum Árbitro para atualizar.');
            return;
        }

        $sequence = DB::table('arbitro_sequences')->first();
        if (!$sequence) {
            $this->error('Tabela arbitro_sequences não inicializada.');
            return;
        }

        $nextNumber = $sequence->next_number;
        $count = 0;

        foreach ($arbitros as $arbitro) {
            /** @var User $arbitro */
            $arbitro->lrv = $nextNumber;
            $arbitro->save();

            $nextNumber++;
            $count++;
        }

        // Atualiza a sequence para o próximo número disponível
        DB::table('arbitro_sequences')->update(['next_number' => $nextNumber, 'updated_at' => now()]);

        $this->info("{$count} Árbitros atualizados com sucesso!");
    }
}
