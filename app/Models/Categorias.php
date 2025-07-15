<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
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
    
}
