<?php

namespace Database\Factories;

use App\Models\Enterprise;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnterpriseFactory extends Factory
{
    protected $model = Enterprise::class;

    public function definition(): array
    {
        return [
            'uuid' => fake()->uuid(),
            'name' => fake()->company(),
            'key' => fake()->unique()->regexify('[A-Za-z0-9]{10}'),
            'status' => true,
            'owner_uuid' => null,
        ];
    }
}