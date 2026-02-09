<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComissaoTecnicaCartao extends Model
{
    protected $table = 'comissao_tecnica_cartoes';

    protected $fillable = [
        'comissao_tecnica_id',
        'ano',
        'impresso',
    ];

    public function comissaoTecnica()
    {
        return $this->belongsTo(ComissaoTecnica::class, 'comissao_tecnica_id');
    }
}
