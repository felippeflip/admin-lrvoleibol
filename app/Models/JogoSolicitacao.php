<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JogoSolicitacao extends Model
{
    protected $fillable = ['jogo_id', 'user_id', 'motivo', 'status'];

    public function jogo()
    {
        return $this->belongsTo(Jogo::class, 'jogo_id', 'jgo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
