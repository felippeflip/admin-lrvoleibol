<?php

namespace App\Http\Controllers;

abstract class Controller
{
    protected function isMobileView()
    {
        $request = request();
        $isMobileDevice = preg_match('/Mobile|Android|BlackBerry|iPhone|Windows Phone/i', $request->userAgent());
        
        if (!$isMobileDevice) {
            return false;
        }

        $user = auth()->user();
        if (!$user) {
            return true; // No login, if mobile device, show mobile login
        }

        // 1. Verifica se o usuário tem um perfil vinculado diretamente (profile_id)
        if ($user->profile && $user->profile->enable_mobile_view) {
            return true;
        }
        
        if (method_exists($user, 'roles') && $user->roles) {
            // 2. Verifica se há vinculação da Role (Função Spatie) com o Profile via tabela auxiliar profile_role
            $roleIds = $user->roles->pluck('id')->toArray();
            if (!empty($roleIds)) {
                $hasRoleLink = \App\Models\Profile::where('enable_mobile_view', true)
                    ->whereHas('roles', function($q) use ($roleIds) {
                        $q->whereIn('roles.id', $roleIds);
                    })->exists();
                
                if ($hasRoleLink) {
                    return true;
                }
            }
            
            // 3. Verifica se o NOME da Função (Role) corresponde ao NOME de um Perfil (Profile) habilitado
            $roleNames = $user->roles->pluck('name')->toArray();
            if (!empty($roleNames)) {
                $hasNameMatch = \App\Models\Profile::where('enable_mobile_view', true)
                    ->whereIn('name', $roleNames)
                    ->exists();
                    
                if ($hasNameMatch) {
                    return true;
                }
            }
        }

        // Se não encontrar nenhuma correspondência que conceda acesso
        return false;
    }
}
