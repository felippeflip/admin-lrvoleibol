<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ComissaoTecnica extends Model
{
    use HasFactory;

    protected $table = 'comissao_tecnicas';

    protected $fillable = [
        'time_id',
        'nome',
        'registro_lrv',
        'cpf',
        'rg',
        'funcao',
        'documento_registro',
        'foto',
        'comprovante_documento',
        'celular',
        'telefone',
        'email',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'cidade',
        'estado',
        'status'
    ];

    public function time()
    {
        return $this->belongsTo(Time::class, 'time_id', 'tim_id');
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return Storage::disk('comissao_fotos')->url($this->foto);
        }
        return asset('images/default-user.png'); // Or generic placeholder
    }

    public function getComprovanteUrlAttribute()
    {
        if ($this->comprovante_documento) {
            return Storage::disk('comissao_docs')->url($this->comprovante_documento);
        }
        return null;
    }
    public function cartoes()
    {
        return $this->hasMany(ComissaoTecnicaCartao::class, 'comissao_tecnica_id');
    }

    public function cartaoImpresso(?int $ano = null): bool
    {
        $ano = $ano ?? date('Y');
        $cartao = $this->cartoes()->where('ano', $ano)->first();
        return $cartao ? (bool) $cartao->impresso : false;
    }
}
