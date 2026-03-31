<?php

namespace Database\Factories;

use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveType>
 */
class LeaveTypeFactory extends Factory
{
    protected $model = LeaveType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Vacation Leave', 'Sick Leave', 'Emergency Leave', 'Personal Leave']),
            'code' => fake()->unique()->lexify('??'),
            'defaultCredits' => fake()->randomFloat(2, 1, 30),
            'description' => fake()->optional()->sentence(),
            'isActive' => true,
            'isPaid' => true,
            'requiresDocument' => false,
            'maxConsecutiveDays' => fake()->optional()->numberBetween(1, 30),
            'gender' => null,
        ];
    }
}
