<?php

namespace App\Services;

use App\Models\Jogo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JogoService
{
    /**
     * Cria um novo jogo na tabela local.
     *
     * @param array $data
     * @return Jogo
     */
    public function create(array $data)
    {
        return DB::transaction(function () use ($data) {
            $jogo = Jogo::create([
                'jgo_eqp_cpo_mandante_id' => $data['mandante_id'],
                'jgo_eqp_cpo_visitante_id' => $data['visitante_id'],
                'jgo_dt_jogo' => $data['data_jogo'],
                'jgo_hora_jogo' => $data['hora_jogo'],
                'jgo_local_jogo_id' => $data['ginasio_id'],
                'jgo_arbitro_principal' => $data['juiz_principal_id'] ?? null,
                'jgo_arbitro_secundario' => $data['juiz_secundario_id'] ?? null,
                'jgo_apontador' => $data['apontador_id'] ?? null,
            ]);

            Log::info("Jogo criado localmente com ID: {$jogo->jgo_id}");

            return $jogo;
        });
    }

    /**
     * Atualiza um jogo existente.
     *
     * @param int $id
     * @param array $data
     * @return Jogo
     */
    public function update($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $jogo = Jogo::findOrFail($id);

            $jogo->update([
                'jgo_eqp_cpo_mandante_id' => $data['mandante_id'],
                'jgo_eqp_cpo_visitante_id' => $data['visitante_id'],
                'jgo_dt_jogo' => $data['data_jogo'],
                'jgo_hora_jogo' => $data['hora_jogo'],
                'jgo_local_jogo_id' => $data['ginasio_id'],
                'jgo_arbitro_principal' => $data['juiz_principal_id'] ?? null,
                'jgo_arbitro_secundario' => $data['juiz_secundario_id'] ?? null,
                'jgo_apontador' => $data['apontador_id'] ?? null,
            ]);

            Log::info("Jogo atualizado localmente com ID: {$jogo->jgo_id}");

            return $jogo;
        });
    }
}
