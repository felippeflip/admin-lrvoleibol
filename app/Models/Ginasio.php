<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ginasio extends Model
{
    use HasFactory;

    protected $table = 'ginasios';
    protected $primaryKey = 'gin_id';

    protected $fillable = [
        'gin_nome',
        'gin_cep',
        'gin_endereco',
        'gin_numero',
        'gin_bairro',
        'gin_cidade',
        'gin_estado',
        'gin_complemento',
        'gin_telefone',
        'gin_email',
        'gin_tim_id'
    ];

    public function time()
    {
        return $this->belongsTo(Time::class, 'gin_tim_id', 'tim_id');
    }

    public function getGoogleMapsLinkAttribute()
    {
        $address = "{$this->gin_endereco}, {$this->gin_numero}, {$this->gin_bairro}, {$this->gin_cidade} - {$this->gin_estado}";
        return 'https://www.google.com/maps/search/?api=1&query=' . urlencode($address);
    }

    public function getWazeLinkAttribute()
    {
        $address = "{$this->gin_endereco}, {$this->gin_numero}, {$this->gin_bairro}, {$this->gin_cidade} - {$this->gin_estado}";
        return 'https://waze.com/ul?q=' . urlencode($address);
    }
}
