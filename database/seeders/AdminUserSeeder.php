<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create a default Admin user if it doesn't exist
        $adminEmail = 'admin@lrvoleibol.com';
        $adminUser = User::where('email', $adminEmail)->first();

        if (!$adminUser) {
            $adminUser = User::create([
                'name' => 'Administrador',
                'email' => $adminEmail,
                'password' => Hash::make('password'), // Change this in production
            ]);
        }

        if (!$adminUser->hasRole('Administrador')) {
            $adminUser->assignRole('Administrador');
        }
    }
}
