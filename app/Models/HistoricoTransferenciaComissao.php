<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoTransferenciaComissao extends Model
{
    protected $table = 'historico_transferencia_comissaos';
    protected $primaryKey = 'htrc_id';

    protected $fillable = [
        'htrc_comissao_id',
        'htrc_tim_origem_id',
        'htrc_tim_destino_id',
        'htrc_user_id',
    ];

    public function comissao()
    {
        return $this->belongsTo(ComissaoTecnica::class, 'htrc_comissao_id', 'id');
    }

    public function timeOrigem()
    {
        return $this->belongsTo(Time::class, 'htrc_tim_origem_id', 'tim_id');
    }

    public function timeDestino()
    {
        return $this->belongsTo(Time::class, 'htrc_tim_destino_id', 'tim_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'htrc_user_id', 'id');
    }
}
