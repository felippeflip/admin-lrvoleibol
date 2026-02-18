<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ComissaoTecnicaRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Create Role
        $role = Role::firstOrCreate(['name' => 'ComissaoTecnica', 'guard_name' => 'web']);

        // 2. Assign Permissions (Example: Can create Atletas, Can edit Atletas)
        // If specific permissions are needed, add them here.
        // For now, checks are mostly role-based in controllers, so role existence is key.
        
        $this->command->info('Role ComissaoTecnica created/verified successfully!');
    }
}
