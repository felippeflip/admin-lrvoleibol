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
}
