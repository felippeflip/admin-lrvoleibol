<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WpPostmeta extends Model
{
    use HasFactory;

    protected $table = 'wp_postmeta';

    protected $primaryKey = 'meta_id';

    public $timestamps = false;

    protected $fillable = [
        'post_id',
        'meta_key',
        'meta_value',
    ];

    public function post()
    {
        return $this->belongsTo(WpPosts::class, 'post_id', 'ID');
    }
}
