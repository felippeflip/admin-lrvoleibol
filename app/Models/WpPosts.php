<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpPosts extends Model
{
    use HasFactory;

    protected $table = 'wp_posts';

    protected $primaryKey = 'ID';

    public $timestamps = false;

    protected $fillable = [
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content', 
        'post_title', 
        'post_excerpt', 
        'post_status', 
        'comment_status', 
        'ping_status', 
        'post_password', 
        'post_name', 
        'to_ping', 
        'pinged', 
        'post_modified', 
        'post_modified_gmt', 
        'post_content_filtered', 
        'post_parent', 
        'guid', 
        'menu_order', 
        'post_type', 
        'post_mime_type', 
        'comment_count',
    ];


    public function meta()
    {
        return $this->hasMany(WpPostmeta::class, 'post_id', 'ID');
    }

    public function termRelationships()
    {
        return $this->hasMany(Wp_Term_Relationships::class, 'object_id', 'ID');
    }

    public function eventTypes()
    {
        return $this->belongsToMany(Wp_Term_Taxonomy::class, 'wp_term_relationships', 'object_id', 'term_taxonomy_id')
                    ->where('taxonomy', 'event_listing_type')
                    ->with('term');
    }
    
    public function eventCategories()
    {
        return $this->belongsToMany(Wp_Term_Taxonomy::class, 'wp_term_relationships', 'object_id', 'term_taxonomy_id')
                    ->where('taxonomy', 'event_listing_category')
                    ->with('term');
    }

    public function getMetaValue($key)
    {
    $meta = $this->meta()->where('meta_key', $key)->first();
    $value = $meta ? $meta->meta_value : null;

    // Tratar a data para o formato YYYY-MM-DD se a chave for _event_start_date ou _event_end_date
    if (in_array($key, ['_event_start_date', '_event_end_date']) && $value) {
        $date = \DateTime::createFromFormat('Y-m-d H:i:s', $value);
        if ($date) {
            return $date->format('Y-m-d');
        }
    }

    return $value;
    }

    

}
