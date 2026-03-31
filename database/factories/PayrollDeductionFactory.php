<?php

namespace Database\Factories;

use App\Models\PayrollDeduction;
use App\Models\PayrollRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollDeductionFactory extends Factory
{
    protected $model = PayrollDeduction::class;

    public function definition(): array
    {
        return [
            'payrollRecordID' => PayrollRecord::factory(),
            'deductionTypeID' => null,
            'employeeLoanID' => null,
            'description' => fake()->sentence(3),
            'employeeAmount' => fake()->randomFloat(2, 100, 2000),
            'employerAmount' => 0,
        ];
    }
}
