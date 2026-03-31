<?php

namespace Database\Factories;

use App\Enums\LeaveStatus;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\LeaveRequest>
 */
class LeaveRequestFactory extends Factory
{
    protected $model = LeaveRequest::class;

    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('+1 week', '+2 months');
        $endDate = fake()->dateTimeBetween($startDate, (clone $startDate)->modify('+5 days'));

        return [
            'employeeID' => Employee::factory(),
            'leaveTypeID' => LeaveType::factory(),
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalDays' => 1.00,
            'isHalfDay' => false,
            'halfDayPeriod' => null,
            'reason' => fake()->sentence(),
            'status' => LeaveStatus::PENDING,
            'approvedByID' => null,
            'approvedAt' => null,
            'rejectionReason' => null,
        ];
    }
}
