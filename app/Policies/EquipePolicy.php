<?php

namespace App\Policies;

use App\Models\Equipe;
use App\Models\User;

class EquipePolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Equipe $equipe)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $equipe->time && $equipe->time->tim_user_id === $user->id;
    }

    public function create(User $user)
    {
        return $user->hasRole('Administrador') || $user->hasRole('ResponsavelTime');
    }

    public function update(User $user, Equipe $equipe)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $equipe->time && $equipe->time->tim_user_id === $user->id;
    }

    public function delete(User $user, Equipe $equipe)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $equipe->time && $equipe->time->tim_user_id === $user->id;
    }
}
