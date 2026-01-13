<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoSet extends Model
{
    use HasFactory;

    protected $table = 'resultado_sets';
    protected $primaryKey = 'set_id';

    protected $fillable = [
        'set_jgo_id',
        'set_numero',
        'set_pontos_mandante',
        'set_pontos_visitante',
        'set_vencedor', // 1 = Mandante, 2 = Visitante? Or use boolean or team ID? User didn't specify enum logic. I'll use simple integer for now or inferred.
                        // User Schema said "set_vencedor int(11)". So likely 1 or 2, or TeamID.
                        // I will store the Team ID or 1/2? Given schema "pontos_mandante", 1=Mandante, 2=Visitante is simpler logic.
    ];

    public function jogo()
    {
        return $this->belongsTo(Jogo::class, 'set_jgo_id', 'jgo_id');
    }
}
