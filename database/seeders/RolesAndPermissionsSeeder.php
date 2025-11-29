<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions (examples)
        // Permission::create(['name' => 'edit articles']);

        // create roles and assign created permissions

        // Role Admin
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        // $roleAdmin->givePermissionTo(Permission::all()); // Give all permissions

        // Role Juiz
        $roleJuiz = Role::firstOrCreate(['name' => 'Juiz']);

        // Role Responsável pelo Time
        $roleRespTime = Role::firstOrCreate(['name' => 'ResponsavelTime']);

        // Create a default Admin user if it doesn't exist
        $adminEmail = 'admin@lrvoleibol.com';
        $adminUser = User::where('email', $adminEmail)->first();

        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Administrador',
                'email' => $adminEmail,
                'password' => Hash::make('password'), // Change this in production
                'is_arbitro' => false,
                'is_resp_time' => false, // Admin is not necessarily a team manager
            ]);
        }
        
        if (!$adminUser->hasRole('Administrador')) {
            $adminUser->assignRole($roleAdmin);
        }
    }
}
