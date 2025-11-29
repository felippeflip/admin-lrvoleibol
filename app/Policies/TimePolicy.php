<?php

namespace App\Policies;

use App\Models\Time;
use App\Models\User;

class TimePolicy
{
    public function viewAny(User $user)
    {
        return true;
    }

    public function view(User $user, Time $time)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $user->id === $time->tim_user_id;
    }

    public function create(User $user)
    {
        return $user->hasRole('Administrador');
    }

    public function update(User $user, Time $time)
    {
        if ($user->hasRole('Administrador')) {
            return true;
        }
        return $user->id === $time->tim_user_id;
    }

    public function delete(User $user, Time $time)
    {
        return $user->hasRole('Administrador');
    }
}
