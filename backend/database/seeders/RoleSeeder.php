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
            'authority' => [
                    'enterprise' => [
                        'read' => true,
                        'edit' => true,
                    ],
                    'personnel' => [
                        'read' => true,
                        'create' => true,
                        'edit' => true,
                        'delete' => true,
                    ],
                ],
            'color_hex' => '#FF5722',
            'is_shared' => true,
            'hierarchy_level' => 1
        ]);

        // Rôle employé (partagé par défaut)
        Role::create([
            'name' => 'Employé',
            'authority' => [
                    'enterprise' => [
                        'read' => false,
                        'edit' => false,
                    ],
                    'personnel' => [
                        'read' => false,
                        'create' => false,
                        'edit' => false,
                        'delete' => false,
                    ],
                ],
            'color_hex' => '#4CAF50',
            'is_shared' => true,
            'hierarchy_level' => 6
        ]);
    }
}