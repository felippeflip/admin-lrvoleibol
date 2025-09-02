<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ElencoEquipeCampeonato extends Pivot
{
    use HasFactory;

    // O nome da tabela
    protected $table = 'elenco_equipe_campeonato';

    // A chave primária personalizada
    protected $primaryKey = 'ele_id';

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'ele_fk_eqp_cpo_id',
        'ele_fk_atl_id',
        'ele_num_camisa',
        'ele_posicao_atuando',
    ];

    /**
     * Define o relacionamento com o modelo EquipeCampeonato
     */
    public function equipeCampeonato()
    {
        return $this->belongsTo(EquipeCampeonato::class, 'ele_fk_eqp_cpo_id', 'eqp_cpo_id');
    }

    /**
     * Define o relacionamento com o modelo Atletas
     */
    public function atleta()
    {
        return $this->belongsTo(Atletas::class, 'ele_fk_atl_id', 'atl_id');
    }
}
