<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wp_Term_Taxonomy extends Model
{
    use HasFactory;

    protected $table = 'wp_term_taxonomy';

    protected $primaryKey = 'term_taxonomy_id';

    public $timestamps = false;


    protected $fillable = [
        'term_taxonomy_id',
        'term_id',
        'taxonomy',  
        'description',       
        'parent',
        'count',
    ];

    public function term()
    {
        return $this->belongsTo(Wp_Terms::class, 'term_id', 'term_id');
    }

    public function relationships()
    {
        return $this->hasMany(Wp_Term_Relationships::class, 'term_taxonomy_id', 'term_taxonomy_id');
    }
}
