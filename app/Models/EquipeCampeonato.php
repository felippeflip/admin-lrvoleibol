<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class EquipeCampeonato extends Pivot
{
    use HasFactory;

    // O nome da tabela
    protected $table = 'equipe_campeonato';
    
    // A chave primária personalizada
    protected $primaryKey = 'eqp_cpo_id';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'cpo_fk_id', 
        'eqp_fk_id', 
        'eqp_cpo_dt_inscricao', 
        'eqp_cpo_classificacaofinal'
    ];

    /**
     * Define o relacionamento com o modelo Equipes
     */
    public function equipe()
    {
        return $this->belongsTo(Equipes::class, 'eqp_fk_id', 'eqp_id');
    }

    /**
     * Define o relacionamento com o modelo Campeonatos
     */
    public function campeonato()
    {
        return $this->belongsTo(Campeonatos::class, 'cpo_fk_id', 'cpo_id');
    }
}
