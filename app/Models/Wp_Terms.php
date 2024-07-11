<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wp_Terms extends Model
{
    use HasFactory;

    protected $table = 'wp_terms';

    protected $primaryKey = 'term_id';

    public $timestamps = false;

    protected $fillable = [
        'term_id',
        'name',
        'slug',  
        'term_group',
    ];

    public function taxonomies()
    {
        return $this->hasMany(Wp_Term_Taxonomy::class, 'term_id', 'term_id');
    }


}
