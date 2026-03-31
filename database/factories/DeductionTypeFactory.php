<?php

namespace Database\Factories;

use App\Models\DeductionType;
use Illuminate\Database\Eloquent\Factories\Factory;

class DeductionTypeFactory extends Factory
{
    protected $model = DeductionType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'code' => fake()->unique()->lexify('???'),
            'computationMethod' => 'fixed',
            'isStatutory' => false,
            'isActive' => true,
        ];
    }
}
