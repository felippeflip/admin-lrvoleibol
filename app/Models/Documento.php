<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'descricao',
        'caminho_arquivo',
        'tipo', // 'pdf', 'imagem'
        'permissao', // 'Todos', 'Administrador', 'Juiz', 'ResponsavelTime'
        'ativo',
    ];

    protected $casts = [
        'ativo' => 'boolean',
    ];

    public function getUrlAttribute()
    {
        return \Illuminate\Support\Facades\Storage::disk('documentos_uploads')->url($this->caminho_arquivo);
    }
}
