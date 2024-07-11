<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wp_Term_Relationships extends Model
{
    use HasFactory;

    protected $table = 'wp_term_relationships';
    protected $primaryKey = ['object_id', 'term_taxonomy_id']; // Assumindo chave primária composta
    public $incrementing = false; // Desabilitar auto-incremento para chave composta
    public $timestamps = false; // Desabilitar timestamps

    protected $fillable = [
        'object_id',
        'term_taxonomy_id',
        'term_order',
    ];

    public function post()
    {
        return $this->belongsTo(WpPosts::class, 'object_id', 'ID');
    }

    public function termTaxonomy()
    {
        return $this->belongsTo(Wp_Term_Taxonomy::class, 'term_taxonomy_id', 'term_taxonomy_id');
    }
}
