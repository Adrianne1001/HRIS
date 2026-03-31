<?php

namespace Database\Factories;

use App\Enums\HolidayType;
use App\Models\Holiday;
use Illuminate\Database\Eloquent\Factories\Factory;

class HolidayFactory extends Factory
{
    protected $model = Holiday::class;

    public function definition(): array
    {
        $date = fake()->dateTimeBetween('2026-01-01', '2026-12-31');
        return [
            'name' => fake()->words(3, true),
            'date' => $date,
            'holidayType' => fake()->randomElement(HolidayType::cases()),
            'year' => (int) $date->format('Y'),
        ];
    }
}
