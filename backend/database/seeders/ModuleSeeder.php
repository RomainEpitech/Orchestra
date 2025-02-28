<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('modules')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Entreprise',
            'description' => 'Module de gestion des informations de l\'entreprise',
            'key' => 'enterprise',
            'is_core' => true,
            'free_limits' => null,
            'price' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('modules')->insert([
            'uuid' => Str::uuid()->toString(),
            'name' => 'Personnel',
            'description' => 'Module de gestion des collaborateurs et des utilisateurs',
            'key' => 'personnel',
            'is_core' => true,
            'free_limits' => json_encode([
                'userLimit' => 10,
            ]),
            'price' => 99.00,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}