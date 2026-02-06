<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage; // Importe o Facade Storage
use Carbon\Carbon; // Para formatação de data
use Illuminate\Support\Facades\Vite;

class Atleta extends Model // Renomeado para Atleta (convenção Laravel)
{
    use HasFactory;

    protected $table = 'atletas'; // Nome da tabela no banco de dados
    protected $primaryKey = 'atl_id'; // Chave primária da tabela

    // Campos que podem ser preenchidos em massa
    protected $fillable = [
        'atl_nome',
        'atl_cpf',
        'atl_rg',
        'atl_celular',
        'atl_telefone',
        'atl_email',
        'atl_sexo',
        'atl_dt_nasc',
        'atl_resg', // Registro Geral ou similar, campo genérico de identificação.
        'atl_endereco',
        'atl_numero',
        'atl_bairro',
        'atl_cidade',
        'atl_estado', // UF
        'atl_cep',
        'atl_categoria',
        'atl_ano_insc',
        'atl_foto', // Nome do arquivo da foto
        'atl_ativo',
        'atl_tim_id',
    ];

    // --- Accessors para formatação de dados ---

    /**
     * Accessor para formatar o CPF para exibição.
     */
    public function getAtlCpfFormattedAttribute()
    {
        $cpf = preg_replace('/[^0-9]/', '', $this->atl_cpf);
        if (strlen($cpf) === 11) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
        }
        return $this->atl_cpf;
    }

    /**
     * Accessor para formatar o RG para exibição.
     */
    public function getAtlRgFormattedAttribute()
    {
        $rg = preg_replace('/[^0-9]/', '', $this->atl_rg);
        // Exemplo de máscara de RG comum (XX.XXX.XXX-X) - ajuste se seu padrão for outro
        if (strlen($rg) === 9) {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{1})/', '$1.$2.$3-$4', $rg);
        } else if (strlen($rg) === 8) { // Ex: XX.XXX.XXX
            return preg_replace('/(\d{2})(\d{3})(\d{3})/', '$1.$2.$3', $rg);
        }
        return $this->atl_rg;
    }

    /**
     * Accessor para formatar o Celular para exibição.
     */
    public function getAtlCelularFormattedAttribute()
    {
        $celular = preg_replace('/[^0-9]/', '', $this->atl_celular);
        if (strlen($celular) === 11) {
            return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $celular);
        } else if (strlen($celular) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $celular);
        }
        return $this->atl_celular;
    }

    /**
     * Accessor para formatar o Telefone para exibição.
     */
    public function getAtlTelefoneFormattedAttribute()
    {
        $telefone = preg_replace('/[^0-9]/', '', $this->atl_telefone);
        if (strlen($telefone) === 10) {
            return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
        }
        return $this->atl_telefone;
    }

    /**
     * Accessor para formatar a Data de Nascimento para exibição (DD/MM/AAAA).
     */
    public function getAtlDtNascFormattedAttribute()
    {
        if ($this->atl_dt_nasc) {
            return Carbon::parse($this->atl_dt_nasc)->format('d/m/Y');
        }
        return null;
    }

    /**
     * Accessor para obter a URL pública da foto do atleta.
     */
    public function getAtlFotoUrlAttribute()
    {
        if ($this->atl_foto) {
            // Certifique-se que 'atletas_fotos' é o disco correto configurado em filesystems.php
            return Storage::disk('atletas_fotos')->url($this->atl_foto);
        }
        // Retorna uma imagem de placeholder se não houver foto
        return asset('images/placeholder-atleta.png'); // Crie esta imagem ou ajuste o caminho
    }

    // Relacionamento com Categoria
    public function categoria()
    {
        return $this->belongsTo(Categoria::class, 'atl_categoria', 'cto_id');
    }

    // Relacionamento com Time
    public function time()
    {
        return $this->belongsTo(Time::class, 'atl_tim_id', 'tim_id');
    }

    // Relacionamento N:N com Equipe_Campeonato (através da tabela elenco_equipe_campeonato)
    public function participacoes()
    {
        return $this->belongsToMany(EquipeCampeonato::class, 'elenco_equipe_campeonato', 'ele_fk_atl_id', 'ele_fk_eqp_cpo_id')
            ->using(ElencoEquipeCampeonato::class)
            ->withPivot('ele_num_camisa', 'ele_posicao_atuando')
            ->withTimestamps();
    }
    // Relacionamento com Cartões
    public function cartoes()
    {
        return $this->hasMany(AtletaCartao::class, 'atc_atl_id', 'atl_id');
    }

    // Helper para verificar se cartão de ano X está impresso
    public function cartaoImpresso(?int $ano = null): bool
    {
        $ano = $ano ?? date('Y');
        $cartao = $this->cartoes()->where('atc_ano', $ano)->first();
        return $cartao ? (bool) $cartao->atc_impresso : false;
    }
}