<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campeonatos extends Model
{
    use HasFactory;

    protected $table = 'campeonatos';
    protected $primaryKey = 'cpo_id';

    protected $fillable = [
        'cpo_nome',
        'cpo_term_tx_id',
        'cpo_ano',
        'cpo_dt_inicio',
        'cpo_dt_fim',
    ];

    // Adicione este relacionamento belongsToMany
    public function equipes()
    {
        return $this->belongsToMany(Equipes::class, 'equipe_campeonato', 'cpo_fk_id', 'eqp_fk_id')
                    ->using(EquipeCampeonato::class)
                    ->withPivot('eqp_cpo_dt_inscricao', 'eqp_cpo_classificacaofinal')
                    ->withTimestamps();
    }
}
