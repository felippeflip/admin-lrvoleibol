<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Time extends Model
{
    use HasFactory;

    protected $table = 'times';
    protected $primaryKey = 'tim_id';

    protected $fillable = [
        'tim_user_id',
        'tim_registro',
        'tim_cnpj',
        'tim_nome',
        'tim_nome_abre',
        'tim_sigla',
        'tim_endereco',
        'tim_numero',
        'tim_bairro',
        'tim_cidade',
        'tim_uf',
        'tim_cep',
        'tim_telefone',
        'tim_celular',
        'tim_email',
        'tim_logo',
        'tim_responsavel'
    ];
    
     /**
     * Relacionamento com o usuário responsável.
     * Assumindo que 'tim_user_id' é a chave estrangeira para a tabela 'users'.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'tim_user_id');
    }

    /**
     * Accessor para formatar o CNPJ para exibição.
     */
    public function getTimCnpjFormattedAttribute()
    {
        $cnpj = preg_replace('/[^0-9]/', '', $this->tim_cnpj); // Remove tudo que não for número
        if (strlen($cnpj) === 14) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $cnpj);
        }
        return $this->tim_cnpj; // Retorna o valor original se não for um CNPJ de 14 dígitos
    }

    /**
     * Accessor para formatar o Celular para exibição.
     */
    public function getTimCelularFormattedAttribute()
    {
        $celular = preg_replace('/[^0-9]/', '', $this->tim_celular); // Remove tudo que não for número
        if (strlen($celular) === 11) { // Ex: (DDD) 9XXXX-XXXX
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $celular);
        } else if (strlen($celular) === 10) { // Ex: (DDD) XXXX-XXXX (sem o 9º dígito)
             return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $celular);
        }
        return $this->tim_celular; // Retorna o valor original
    }

    /**
     * Accessor para formatar o Telefone para exibição.
     */
    public function getTimTelefoneFormattedAttribute()
    {
        $telefone = preg_replace('/[^0-9]/', '', $this->tim_telefone); // Remove tudo que não for número
        if (strlen($telefone) === 11) { // Ex: (DDD) 9XXXX-XXXX
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
        }
        return $this->tim_telefone; // Retorna o valor original
    }

    /**
     * Get the team's logo URL.
     *
     * @return string|null
     */
    public function getTimLogoUrlAttribute()
    {
        // Verifica se existe um nome de arquivo para a logo
        if ($this->tim_logo) {
            // Usa o disco 'team_logos' para gerar a URL pública
            return Storage::disk('times_logos')->url($this->tim_logo);
        }

        // Retorna null ou uma imagem de placeholder se não houver logo
        return null;
        // Ou, para um placeholder:
        // return asset('images/placeholder-logo.png');
    }
}
