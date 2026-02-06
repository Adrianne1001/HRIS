<?php

namespace Database\Factories;

use App\Models\WorkSchedule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkSchedule>
 */
class WorkScheduleFactory extends Factory
{
    protected $model = WorkSchedule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->randomElement([
                'Regular Day Shift',
                'Morning Shift',
                'Afternoon Shift',
                'Night Shift',
                'Flexible Hours',
                'Part-time Morning',
                'Part-time Afternoon',
            ]),
            'startTime' => fake()->randomElement(['06:00', '07:00', '08:00', '09:00', '14:00', '22:00']),
            'endTime' => fake()->randomElement(['15:00', '16:00', '17:00', '18:00', '23:00', '06:00']),
            'startBreakTime' => '12:00',
            'endBreakTime' => '13:00',
            'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
        ];
    }

    /**
     * Standard 9-5 work schedule.
     */
    public function standard(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Standard 9-5',
            'startTime' => '09:00',
            'endTime' => '17:00',
            'startBreakTime' => '12:00',
            'endBreakTime' => '13:00',
            'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
        ]);
    }

    /**
     * Night shift work schedule.
     */
    public function nightShift(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Night Shift',
            'startTime' => '22:00',
            'endTime' => '06:00',
            'startBreakTime' => '02:00',
            'endBreakTime' => '03:00',
            'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
        ]);
    }

    /**
     * Flexible work schedule with no fixed break.
     */
    public function flexible(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Flexible Hours',
            'startTime' => '08:00',
            'endTime' => '17:00',
            'startBreakTime' => null,
            'endBreakTime' => null,
            'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
        ]);
    }
}
