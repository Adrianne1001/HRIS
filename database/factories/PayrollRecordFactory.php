<?php

namespace Database\Factories;

use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollRecordFactory extends Factory
{
    protected $model = PayrollRecord::class;

    public function definition(): array
    {
        $basicPay = fake()->randomFloat(2, 7500, 50000);
        $grossPay = $basicPay;

        return [
            'payrollPeriodID' => PayrollPeriod::factory(),
            'employeeID' => Employee::factory(),
            'basicMonthlySalary' => $basicPay * 2,
            'dailyRate' => round($basicPay * 2 / 22, 2),
            'hourlyRate' => round($basicPay * 2 / 22 / 8, 2),
            'daysWorked' => 11,
            'daysAbsent' => 0,
            'approvedLeaveDays' => 0,
            'regularHoursWorked' => 88,
            'overtimeHours' => 0,
            'nightDifferentialHours' => 0,
            'holidayDaysWorked' => 0,
            'specialHolidayDaysWorked' => 0,
            'lateMinutes' => 0,
            'undertimeMinutes' => 0,
            'basicPay' => $basicPay,
            'absentDeduction' => 0,
            'lateDeduction' => 0,
            'undertimeDeduction' => 0,
            'overtimePay' => 0,
            'nightDifferentialPay' => 0,
            'holidayPay' => 0,
            'specialHolidayPay' => 0,
            'grossPay' => $grossPay,
            'sssEmployee' => 0,
            'sssEmployer' => 0,
            'sssEC' => 0,
            'philhealthEmployee' => 0,
            'philhealthEmployer' => 0,
            'pagibigEmployee' => 0,
            'pagibigEmployer' => 0,
            'withholdingTax' => 0,
            'totalMandatoryDeductions' => 0,
            'totalLoanDeductions' => 0,
            'totalDeductions' => 0,
            'netPay' => $grossPay,
        ];
    }
}
