<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jogo extends Model
{
    use HasFactory;

    protected $table = 'jogos';
    protected $primaryKey = 'jgo_id';

    protected $fillable = [
        'jgo_wp_id',
        'jgo_status',
        'jgo_res_status',
        'jgo_res_usuario_id',
        'jgo_res_data_envio',
        'jgo_vencedor_mandante',
        'jgo_eqp_cpo_mandante_id',
        'jgo_eqp_cpo_visitante_id',
        'jgo_dt_jogo',
        'jgo_hora_jogo',
        'jgo_local_jogo_id',
        'jgo_arbitro_principal',
        'jgo_arbitro_secundario',
        'jgo_apontador',
        'jgo_notificacao_arbitro_p',
        'jgo_notificacao_arbitro_s',
        'jgo_notificacao_apontador',
        'jgo_notificacao_resultado',
    ];

    public function resultadoSets()
    {
        return $this->hasMany(ResultadoSet::class, 'set_jgo_id', 'jgo_id');
    }

    public function usuarioEnvio()
    {
        return $this->belongsTo(User::class, 'jgo_res_usuario_id');
    }

    /**
     * Relacionamento com a Equipe Mandante (EquipeCampeonato)
     */
    public function mandante()
    {
        return $this->belongsTo(EquipeCampeonato::class, 'jgo_eqp_cpo_mandante_id', 'eqp_cpo_id');
    }

    /**
     * Relacionamento com a Equipe Visitante (EquipeCampeonato)
     */
    public function visitante()
    {
        return $this->belongsTo(EquipeCampeonato::class, 'jgo_eqp_cpo_visitante_id', 'eqp_cpo_id');
    }

    /**
     * Relacionamento com o Ginásio (Local do Jogo)
     */
    public function ginasio()
    {
        return $this->belongsTo(Ginasio::class, 'jgo_local_jogo_id', 'gin_id');
    }

    /**
     * Relacionamento com o Árbitro Principal (User)
     */
    public function arbitroPrincipal()
    {
        return $this->belongsTo(User::class, 'jgo_arbitro_principal', 'id');
    }

    /**
     * Relacionamento com o Árbitro Secundário (User)
     */
    public function arbitroSecundario()
    {
        return $this->belongsTo(User::class, 'jgo_arbitro_secundario', 'id');
    }

    /**
     * Relacionamento com o Apontador (User)
     */
    public function apontador()
    {
        return $this->belongsTo(User::class, 'jgo_apontador', 'id');
    }
}
