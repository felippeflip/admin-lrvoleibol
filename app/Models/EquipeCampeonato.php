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
        return $this->belongsTo(Equipe::class, 'eqp_fk_id', 'eqp_id');
    }

    /**
     * Define o relacionamento com o modelo Campeonatos
     */
    public function campeonato()
    {
        return $this->belongsTo(Campeonato::class, 'cpo_fk_id', 'cpo_id');
    }

    /**
     * Define o relacionamento com o Elenco (Atletas inscritos neste campeonato por esta equipe)
     */
    public function elenco()
    {
        return $this->hasMany(ElencoEquipeCampeonato::class, 'ele_fk_eqp_cpo_id', 'eqp_cpo_id');
    }

    /**
     * Relacionamento com jogos onde a equipe é mandante
     */
    public function jogosMandante()
    {
        return $this->hasMany(Jogo::class, 'jgo_eqp_cpo_mandante_id', 'eqp_cpo_id');
    }

    /**
     * Relacionamento com jogos onde a equipe é visitante
     */
    public function jogosVisitante()
    {
        return $this->hasMany(Jogo::class, 'jgo_eqp_cpo_visitante_id', 'eqp_cpo_id');
    }
}
