<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtletaCartao extends Model
{
    protected $table = 'atleta_cartoes';
    protected $primaryKey = 'atc_id';

    protected $fillable = [
        'atc_atl_id',
        'atc_ano',
        'atc_impresso',
    ];

    public function atleta()
    {
        return $this->belongsTo(Atleta::class, 'atc_atl_id', 'atl_id');
    }
}
