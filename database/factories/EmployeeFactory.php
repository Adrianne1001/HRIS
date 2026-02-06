<?php

namespace Database\Factories;

use App\Enums\Department;
use App\Enums\EmploymentStatus;
use App\Enums\EmploymentType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Position;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'userID' => User::factory(),
            'dateOfBirth' => fake()->dateTimeBetween('-60 years', '-18 years'),
            'gender' => fake()->randomElement(Gender::cases()),
            'maritalStatus' => fake()->randomElement(MaritalStatus::cases()),
            'address' => fake()->address(),
            'phoneNbr' => fake()->numerify('09#########'),
            'hireDate' => fake()->dateTimeBetween('-10 years', 'now'),
            'employmentStatus' => fake()->randomElement(EmploymentStatus::cases()),
            'employmentType' => fake()->randomElement(EmploymentType::cases()),
            'department' => fake()->randomElement(Department::cases()),
            'jobTitle' => fake()->randomElement(Position::cases()),
            'basicMonthlySalary' => fake()->randomFloat(2, 15000, 100000),
            'emergencyContactName' => fake()->optional()->name(),
            'emergencyContactPhoneNbr' => fake()->numerify('09#########'),
            'profilePic' => null,
        ];
    }
}
