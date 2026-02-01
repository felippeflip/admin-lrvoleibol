<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',  
        'apelido',       
        'telefone',
        'cpf',
        'cref',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'estado',
        'cep',
        'is_arbitro',
        'is_resp_time',
        'tipo_arbitro',
        'active',
        'foto',
        'data_nascimento',
        'rg',
        'lrv',
        'time_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class);
    }
    
    public function time()
    {
        return $this->belongsTo(Time::class, 'time_id', 'tim_id');
    }

    public function getFotoUrlAttribute()
    {
        if ($this->foto) {
            return Storage::disk('user_fotos')->url($this->foto);
        }
        return asset('images/placeholder-atleta.png');
    }
}
