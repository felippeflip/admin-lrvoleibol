<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Builder;

class Jogo extends Model
{
    use HasFactory;

    public function scopeAprovadosOuNormais(Builder $query)
    {
        return $query->where(function ($q) {
            $q->whereNull('jgo_status_agendamento')
              ->orWhereNotIn('jgo_status_agendamento', ['pendente_preenchimento', 'pendente_aprovacao']);
        });
    }

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
        'jgo_status_agendamento',
        'jgo_sugerido_por_equipe_id',
        'jgo_fase',
        'jgo_fase_tipo',
        'jgo_status_publicacao',
        'jgo_numero_jogo',
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

    public function solicitacoesAlteracao()
    {
        return $this->hasMany(JogoSolicitacao::class, 'jogo_id', 'jgo_id');
    }

    /**
     * Verifica se o jogo pertence a uma fase eliminatória/playoff
     */
    public function isPlayoff(): bool
    {
        $tipo = $this->jgo_fase_tipo ?? '';
        return in_array($tipo, [
            'semi_final_ouro', 'final_ouro', 'disputa_3_ouro',
            'semi_final_prata', 'final_prata', 'disputa_3_prata',
            'semi_final_bronze', 'final_bronze', 'disputa_3_bronze',
            'semi_final_especial', 'final_especial', 'disputa_3_especial',
            'quartas_de_final'
        ]) || str_contains($tipo, 'semi_final') || str_contains($tipo, 'final') || str_contains($tipo, 'especial');
    }

    /**
     * Retorna a descrição amigável do tipo de fase
     */
    public function getFaseTipoFormatadaAttribute(): string
    {
        $tipos = [
            'classificatoria' => 'Fase Classificatória',
            'quartas_de_final' => 'Quartas de Final',
            'semi_final_ouro' => 'Semi-Final (Série Ouro)',
            'final_ouro' => 'Final (Série Ouro)',
            'disputa_3_ouro' => 'Disputa 3º e 4º Lugar (Série Ouro)',
            'semi_final_prata' => 'Semi-Final (Série Prata)',
            'final_prata' => 'Final (Série Prata)',
            'disputa_3_prata' => 'Disputa 3º e 4º Lugar (Série Prata)',
            'semi_final_bronze' => 'Semi-Final (Série Bronze)',
            'final_bronze' => 'Final (Série Bronze)',
            'disputa_3_bronze' => 'Disputa 3º e 4º Lugar (Série Bronze)',
            'semi_final_especial' => 'Semi-Final (Série Especial)',
            'final_especial' => 'Final (Série Especial)',
            'disputa_3_especial' => 'Disputa 3º e 4º Lugar (Série Especial)',
        ];

        return $tipos[$this->jgo_fase_tipo] ?? str_replace('_', ' ', $this->jgo_fase_tipo ?? '');
    }

    /**
     * Retorna o Golden Set (set 6) se houver
     */
    public function goldenSet()
    {
        return $this->resultadoSets->firstWhere('set_numero', 6);
    }

    /**
     * Retorna apenas os sets regulares (1 a 5)
     */
    public function setsRegulares()
    {
        return $this->resultadoSets->where('set_numero', '<=', 5);
    }
}
