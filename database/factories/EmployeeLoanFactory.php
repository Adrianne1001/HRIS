<?php

namespace Database\Factories;

use App\Enums\LoanType;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeLoanFactory extends Factory
{
    protected $model = EmployeeLoan::class;

    public function definition(): array
    {
        $loanAmount = fake()->randomFloat(2, 5000, 50000);
        $term = fake()->numberBetween(6, 24);
        $monthlyAmortization = round($loanAmount / $term, 2);

        return [
            'employeeID' => Employee::factory(),
            'loanType' => fake()->randomElement(LoanType::cases()),
            'referenceNbr' => fake()->optional()->numerify('LN-########'),
            'loanAmount' => $loanAmount,
            'monthlyAmortization' => $monthlyAmortization,
            'totalPaid' => 0,
            'remainingBalance' => $loanAmount,
            'startDate' => fake()->dateTimeBetween('-6 months', 'now'),
            'endDate' => fake()->optional()->dateTimeBetween('+6 months', '+2 years'),
            'isActive' => true,
            'remarks' => fake()->optional()->sentence(),
        ];
    }
}
