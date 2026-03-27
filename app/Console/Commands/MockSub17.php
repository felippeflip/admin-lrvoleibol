<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Jogo;
use App\Models\ResultadoSet;
use App\Models\Categoria;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class MockSub17 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mock:sub17 {--revert : Reverte os dados mockados} {--categoria= : ID ou Nome da categoria, ex "Sub-17 Feminino"}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mocka resultados para todos os jogos de uma categoria (por padrão Sub-17 Feminino) e permite reverter.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $backupFile = 'mock_backup.json';

        if ($this->option('revert')) {
            $this->info("Revertendo mock anterior...");
            if (!Storage::disk('local')->exists($backupFile)) {
                $this->error("Arquivo de backup não encontrado. Nada a reverter.");
                return;
            }

            $backup = json_decode(Storage::disk('local')->get($backupFile), true);

            DB::transaction(function () use ($backup) {
                // Delete created sets
                if (!empty($backup['created_set_ids'])) {
                    ResultadoSet::whereIn('set_id', $backup['created_set_ids'])->delete();
                }

                // Restore games
                if (!empty($backup['modified_game_ids'])) {
                    Jogo::whereIn('jgo_id', $backup['modified_game_ids'])->update([
                        'jgo_res_status' => null,
                        'jgo_vencedor_mandante' => null
                    ]);
                }
            });

            Storage::disk('local')->delete($backupFile);
            $this->info("Reversão concluída com sucesso!");
            return;
        }

        // ====== MOCKING PROCESS ======
        $catInput = $this->option('categoria') ?: 'Sub-17 Feminino';

        // Tenta achar categoria
        $categoria = Categoria::where('cto_id', $catInput)->orWhere('cto_nome', 'like', "%{$catInput}%")->first();

        if (!$categoria) {
            $this->error("Categoria '$catInput' não encontrada.");
            return;
        }

        $this->info("Buscando jogos (sem resultados aprovados) para {$categoria->cto_nome}...");

        $jogos = Jogo::whereHas('mandante.equipe', function ($q) use ($categoria) {
            $q->where('eqp_categoria_id', $categoria->cto_id);
        })
        ->where(function($q) {
            $q->whereNull('jgo_res_status')
              ->orWhereNotIn('jgo_res_status', ['aprovado']);
        })
        ->get();

        if ($jogos->isEmpty()) {
            $this->warn("Nenhum jogo pendente encontrado para essa categoria.");
            return;
        }

        $this->info("Encontrados {$jogos->count()} jogos. Mockando resultados (Vitória Mandante 3x0)...");

        $modifiedGameIds = [];
        $createdSetIds = [];

        DB::transaction(function () use ($jogos, &$modifiedGameIds, &$createdSetIds) {
            foreach ($jogos as $jogo) {
                // Randomizar levemente os resultados dos sets (ex: 25x15, 25x20) - 3 sets pro mandante
                for ($i = 1; $i <= 3; $i++) {
                    $set = ResultadoSet::create([
                        'set_jgo_id' => $jogo->jgo_id,
                        'set_numero' => $i,
                        'set_pontos_mandante' => 25,
                        'set_pontos_visitante' => rand(10, 23),
                    ]);
                    $createdSetIds[] = $set->set_id;
                }

                $jogo->update([
                    'jgo_res_status' => 'aprovado',
                    'jgo_vencedor_mandante' => 1
                ]);
                $modifiedGameIds[] = $jogo->jgo_id;
            }
        });

        // Save backup
        Storage::disk('local')->put($backupFile, json_encode([
            'modified_game_ids' => $modifiedGameIds,
            'created_set_ids' => $createdSetIds,
            'categoria_id' => $categoria->cto_id
        ]));

        $this->info("Processo concluído! Os jogos foram aprovados. Para reverter, rode com --revert");
    }
}
