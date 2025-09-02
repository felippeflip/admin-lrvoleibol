<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Time;
use App\Models\Categorias;
use App\Models\Campeonatos;
use App\Models\EquipeCampeonato; // Importar o modelo da tabela pivot

class Equipes extends Model
{
    use HasFactory;

    protected $table = 'equipes';
    protected $primaryKey = 'eqp_id';

    protected $fillable = [
        'eqp_time_id',
        'eqp_categoria_id',
        'eqp_nome_detalhado',
        'eqp_nome_treinador',
    ];

    // Define o relacionamento com o modelo Time
    public function time()
    {
        return $this->belongsTo(Time::class, 'eqp_time_id', 'tim_id');
    }

    // Define o relacionamento com o modelo Categoria
    public function categoria()
    {
        return $this->belongsTo(Categorias::class, 'eqp_categoria_id', 'cto_id');
    }

    // Relacionamento N:N com campeonatos através da tabela intermediária equipe_campeonato
    public function campeonatos()
    {
        return $this->belongsToMany(Campeonatos::class, 'equipe_campeonato', 'eqp_fk_id', 'cpo_fk_id')
                    ->using(EquipeCampeonato::class)
                    ->withPivot('eqp_cpo_dt_inscricao', 'eqp_cpo_classificacaofinal')
                    ->withTimestamps();
    }

}
