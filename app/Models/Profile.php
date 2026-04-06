<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'enable_mobile_view'];

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'profile_role');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
