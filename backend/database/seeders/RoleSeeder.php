<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Rôle administrateur (partagé par défaut)
        Role::create([
            'name' => 'Administrateur',
            'authority' => json_encode([
                'full_access' => true,
                'manage_users' => true,
                'manage_roles' => true,
                // Autres permissions selon votre système
            ]),
            'color_hex' => '#FF5722',
            'is_shared' => true,
        ]);

        // Rôle employé (partagé par défaut)
        Role::create([
            'name' => 'Employé',
            'authority' => json_encode([
                'full_access' => false,
                'manage_users' => false,
                'manage_roles' => false,
                // Permissions limitées
            ]),
            'color_hex' => '#4CAF50',
            'is_shared' => true,
        ]);
    }
}