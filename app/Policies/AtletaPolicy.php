<?php

namespace App\Policies;

use App\Models\atleta;
use App\Models\User;
use App\Models\Time;

class AtletaPolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, atleta $atleta)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $atleta->atl_tim_id && Time::where('tim_id', $atleta->atl_tim_id)->where('tim_user_id', $user->id)->exists();
    }

    public function create(User $user)
    {
        return $user->hasRole('Administrador') || $user->hasRole('ResponsavelTime');
    }

    public function update(User $user, atleta $atleta)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $atleta->atl_tim_id && Time::where('tim_id', $atleta->atl_tim_id)->where('tim_user_id', $user->id)->exists();
    }

    public function delete(User $user, atleta $atleta)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $atleta->atl_tim_id && Time::where('tim_id', $atleta->atl_tim_id)->where('tim_user_id', $user->id)->exists();
    }
}
