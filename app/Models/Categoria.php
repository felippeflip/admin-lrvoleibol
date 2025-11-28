<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;

    protected $table = 'categorias';
    protected $primaryKey = 'cto_id';


    protected $fillable = [
        'cto_nome',
        'cto_slug',
        'cto_term_tx_id',
        'cto_descricao',
    ];

    /**
     * Define o relacionamento reverso com o modelo Equipes.
     * Uma categoria pode ter muitas equipes.
     */
    public function equipes()
    {
        return $this->hasMany(Equipes::class, 'eqp_categoria_id', 'cto_id');
    }
}
