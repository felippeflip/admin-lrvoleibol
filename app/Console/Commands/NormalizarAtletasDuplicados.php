<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Atleta;
use App\Models\HistoricoTransferencia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class NormalizarAtletasDuplicados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:normalizar-atletas-duplicados';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Procura por atletas com o mesmo CPF, unifica para o registro mais recente (ou mais completo) e gera o histórico de transferências dos times antigos.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Iniciando normalização de atletas duplicados...");

        // Pegar todos os CPFs que aparecem mais de uma vez (ignorando nulos/vazios)
        $cpfDuplicados = Atleta::select('atl_cpf', DB::raw('count(*) as total'))
            ->whereNotNull('atl_cpf')
            ->where('atl_cpf', '<>', '')
            ->groupBy('atl_cpf')
            ->having('total', '>', 1)
            ->get();

        if ($cpfDuplicados->isEmpty()) {
            $this->info("Nenhum CPF duplicado encontrado. Banco de dados já está normalizado.");
            return;
        }

        $this->info("Encontrados {$cpfDuplicados->count()} CPFs duplicados. Processando...");

        DB::beginTransaction();
        try {
            foreach ($cpfDuplicados as $duplicado) {
                $cpf = $duplicado->atl_cpf;
                $this->line("Processando CPF: {$cpf}");

                // Busca todos os registros desse CPF ordenados por criação (mais antigo primeiro)
                $registros = Atleta::where('atl_cpf', $cpf)
                    ->orderBy('created_at', 'asc')
                    ->get();

                // O registro "Principal" que vai ser mantido será o último (mais recente criado)
                $principal = $registros->last();

                $this->info("  -> Mantendo ID: {$principal->atl_id} (Time Destino: " . ($principal->atl_tim_id ?? 'Nenhum') . ")");

                // Iterar sobre os extras (que serão deletados)
                foreach ($registros as $registro) {
                    if ($registro->atl_id == $principal->atl_id) {
                        continue; // Pula o principal
                    }

                    // Gera o histórico do time antigo para o time atual (se forem diferentes)
                    if ($registro->atl_tim_id && ($registro->atl_tim_id != $principal->atl_tim_id)) {
                        HistoricoTransferencia::create([
                            'htr_atl_id' => $principal->atl_id,
                            'htr_tim_origem_id' => $registro->atl_tim_id,
                            'htr_tim_destino_id' => $principal->atl_tim_id,
                            // Podemos colocar a data do histórico como a data de criação do "principal"
                            'created_at' => $principal->created_at,
                            'updated_at' => $principal->created_at,
                        ]);
                        $this->line("    => Histórico criado: Time {$registro->atl_tim_id} -> Time {$principal->atl_tim_id}");
                    }

                    // Se o principal não tem foto/doc e o antigo tem, podemos migrar os arquivos (opcional para manter a consistência)
                    if (!$principal->atl_foto && $registro->atl_foto) {
                        $principal->atl_foto = $registro->atl_foto;
                        $principal->save();
                    } else if ($principal->atl_foto && $registro->atl_foto && $principal->atl_foto != $registro->atl_foto) {
                        // Deleta arquivo antigo que não vai ser mais usado
                        if (Storage::disk('atletas_fotos')->exists($registro->atl_foto)) {
                            Storage::disk('atletas_fotos')->delete($registro->atl_foto);
                        }
                    }

                    if (!$principal->atl_documento && $registro->atl_documento) {
                        $principal->atl_documento = $registro->atl_documento;
                        $principal->save();
                    } else if ($principal->atl_documento && $registro->atl_documento && $principal->atl_documento != $registro->atl_documento) {
                        if (Storage::disk('doc_atletas')->exists($registro->atl_documento)) {
                            Storage::disk('doc_atletas')->delete($registro->atl_documento);
                        }
                    }

                    // Deletar o registro duplicado
                    $registro->delete();
                    $this->line("    => Registro deletado: ID {$registro->atl_id}");
                }
            }

            DB::commit();
            $this->info("Normalização concluída com sucesso!");

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Erro durante a normalização: " . $e->getMessage());
            Log::error("Erro no comando normalizar-atletas-duplicados: " . $e->getTraceAsString());
        }
    }
}

