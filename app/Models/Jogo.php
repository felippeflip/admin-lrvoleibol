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
     * Retorna o nome da Fase principal do jogo
     * (Fase Classificatória | Série Especial | Série Ouro | Série Prata | Série Bronze)
     */
    public function getFasePrincipalAttribute(): string
    {
        $tipo = strtolower($this->jgo_fase_tipo ?? '');
        $fase = strtolower($this->jgo_fase ?? '');

        if (str_contains($tipo, 'especial') || str_contains($fase, 'especial')) {
            return 'Série Especial';
        }
        if (str_contains($tipo, 'ouro') || str_contains($fase, 'ouro')) {
            return 'Série Ouro';
        }
        if (str_contains($tipo, 'prata') || str_contains($fase, 'prata')) {
            return 'Série Prata';
        }
        if (str_contains($tipo, 'bronze') || str_contains($fase, 'bronze')) {
            return 'Série Bronze';
        }
        if (str_contains($tipo, 'quartas') || str_contains($fase, 'quartas')) {
            return 'Quartas de Final';
        }

        return 'Fase Classificatória';
    }

    /**
     * Retorna a etapa eliminatória específica (Semi-Final, Final, Disputa 3º/4º Lugar, Quartas de Final)
     */
    public function getFaseEtapaAttribute(): ?string
    {
        $tipo = strtolower($this->jgo_fase_tipo ?? '');
        $fase = strtolower($this->jgo_fase ?? '');

        // 1. Checa disputa de 3º e 4º lugar
        if (str_contains($tipo, 'disputa') || str_contains($fase, 'disputa') || str_contains($fase, '3º') || str_contains($fase, '3/4')) {
            return 'Disputa 3º/4º Lugar';
        }

        // 2. Checa semifinal (precisa vir antes de 'final' pois 'semi_final' contém 'final')
        if (str_contains($tipo, 'semi') || str_contains($fase, 'semi')) {
            return 'Semi-Final';
        }

        // 3. Checa final
        if (str_contains($tipo, 'final') || str_contains($fase, 'final')) {
            if (!str_contains($tipo, 'classificatoria') && !str_contains($fase, 'classificatoria')) {
                return 'Final';
            }
        }

        // 4. Checa quartas
        if (str_contains($tipo, 'quartas') || str_contains($fase, 'quartas')) {
            return 'Quartas de Final';
        }

        return null;
    }

    /**
     * Retorna a descrição completa da fase e etapa combinadas
     */
    public function getFaseCompletaAttribute(): string
    {
        $principal = $this->fase_principal;
        $etapa = $this->fase_etapa;

        if ($etapa) {
            if ($principal === 'Quartas de Final' || $principal === 'Fase Classificatória') {
                return $etapa;
            }
            return "{$principal} • {$etapa}";
        }

        return $principal;
    }

    /**
     * Retorna as classes Tailwind para o badge da etapa eliminatória
     */
    public function getFaseEtapaBadgeClassAttribute(): string
    {
        $etapa = $this->fase_etapa;
        if ($etapa === 'Final') {
            return 'bg-red-100 text-red-800 dark:bg-red-900/60 dark:text-red-200 border border-red-300 dark:border-red-700';
        }
        if ($etapa === 'Semi-Final') {
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-200 border border-amber-300 dark:border-amber-700';
        }
        if ($etapa === 'Disputa 3º/4º Lugar') {
            return 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/60 dark:text-emerald-200 border border-emerald-300 dark:border-emerald-700';
        }
        return 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-200 border border-blue-300 dark:border-blue-700';
    }

    /**
     * Retorna o ícone correspondente à fase
     */
    public function getFaseIconeAttribute(): string
    {
        $tipo = strtolower($this->jgo_fase_tipo ?? '');
        $fase = strtolower($this->jgo_fase ?? '');

        if (str_contains($tipo, 'especial') || str_contains($fase, 'especial')) return '⭐';
        if (str_contains($tipo, 'ouro') || str_contains($fase, 'ouro')) return '🏆';
        if (str_contains($tipo, 'prata') || str_contains($fase, 'prata')) return '🥈';
        if (str_contains($tipo, 'bronze') || str_contains($fase, 'bronze')) return '🥉';
        if (str_contains($tipo, 'quartas') || str_contains($fase, 'quartas')) return '⚔️';

        return '🏐';
    }

    /**
     * Retorna as classes Tailwind para o badge da fase
     */
    public function getFaseBadgeClassAttribute(): string
    {
        $tipo = strtolower($this->jgo_fase_tipo ?? '');
        $fase = strtolower($this->jgo_fase ?? '');

        if (str_contains($tipo, 'especial') || str_contains($fase, 'especial')) {
            return 'bg-sky-100 text-sky-800 dark:bg-sky-900/60 dark:text-sky-300 border border-sky-300 dark:border-sky-700';
        }
        if (str_contains($tipo, 'ouro') || str_contains($fase, 'ouro')) {
            return 'bg-amber-100 text-amber-800 dark:bg-amber-900/60 dark:text-amber-300 border border-amber-300 dark:border-amber-700';
        }
        if (str_contains($tipo, 'prata') || str_contains($fase, 'prata')) {
            return 'bg-slate-100 text-slate-800 dark:bg-slate-700 dark:text-slate-200 border border-slate-300 dark:border-slate-600';
        }
        if (str_contains($tipo, 'bronze') || str_contains($fase, 'bronze')) {
            return 'bg-orange-100 text-orange-800 dark:bg-orange-900/60 dark:text-orange-300 border border-orange-300 dark:border-orange-700';
        }
        if (str_contains($tipo, 'quartas') || str_contains($fase, 'quartas')) {
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900/60 dark:text-blue-300 border border-blue-300 dark:border-blue-700';
        }

        return 'bg-blue-50 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300 border border-blue-200 dark:border-blue-800';
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

    /**
     * Retorna o placar geral dos sets calculados
     */
    public function getPlacarAttribute()
    {
        if (!$this->resultadoSets || $this->resultadoSets->isEmpty()) {
            return null;
        }

        $mandante = 0;
        $visitante = 0;

        foreach ($this->resultadoSets as $set) {
            if ($set->set_pontos_mandante !== null && $set->set_pontos_visitante !== null) {
                if ($set->set_pontos_mandante > $set->set_pontos_visitante) {
                    $mandante++;
                } elseif ($set->set_pontos_visitante > $set->set_pontos_mandante) {
                    $visitante++;
                }
            }
        }

        if ($mandante === 0 && $visitante === 0) {
            return null;
        }

        return [
            'mandante' => $mandante,
            'visitante' => $visitante,
            'vencedor' => $mandante > $visitante ? 'mandante' : ($visitante > $mandante ? 'visitante' : 'empate'),
        ];
    }
}
