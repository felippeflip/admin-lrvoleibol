<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistoricoTransferencia extends Model
{
    protected $table = 'historico_transferencias';
    protected $primaryKey = 'htr_id';

    protected $fillable = [
        'htr_atl_id',
        'htr_tim_origem_id',
        'htr_tim_destino_id',
        'htr_user_id',
    ];

    public function atleta()
    {
        return $this->belongsTo(Atleta::class, 'htr_atl_id', 'atl_id');
    }

    public function timeOrigem()
    {
        return $this->belongsTo(Time::class, 'htr_tim_origem_id', 'tim_id');
    }

    public function timeDestino()
    {
        return $this->belongsTo(Time::class, 'htr_tim_destino_id', 'tim_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'htr_user_id', 'id');
    }
}
