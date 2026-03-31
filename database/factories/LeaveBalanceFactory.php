<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveBalance>
 */
class LeaveBalanceFactory extends Factory
{
    protected $model = LeaveBalance::class;

    public function definition(): array
    {
        return [
            'employeeID' => Employee::factory(),
            'leaveTypeID' => LeaveType::factory(),
            'year' => now()->year,
            'totalCredits' => 15.00,
            'usedCredits' => 0,
            'pendingCredits' => 0,
        ];
    }
}
