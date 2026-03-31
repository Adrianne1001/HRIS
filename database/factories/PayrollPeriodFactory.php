<?php

namespace Database\Factories;

use App\Enums\PayrollPeriodStatus;
use App\Enums\PayrollPeriodType;
use App\Models\PayrollPeriod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollPeriodFactory extends Factory
{
    protected $model = PayrollPeriod::class;

    public function definition(): array
    {
        $periodType = fake()->randomElement(PayrollPeriodType::cases());
        $year = 2026;
        $month = fake()->numberBetween(1, 12);

        if ($periodType === PayrollPeriodType::FIRST_HALF) {
            $startDate = sprintf('%d-%02d-01', $year, $month);
            $endDate = sprintf('%d-%02d-15', $year, $month);
        } else {
            $startDate = sprintf('%d-%02d-16', $year, $month);
            $endDate = \Carbon\Carbon::create($year, $month)->endOfMonth()->format('Y-m-d');
        }

        $monthName = \Carbon\Carbon::parse($startDate)->format('F');
        $startDay = \Carbon\Carbon::parse($startDate)->day;
        $endDay = \Carbon\Carbon::parse($endDate)->day;

        return [
            'name' => "{$monthName} {$startDay}-{$endDay}, {$year}",
            'periodType' => $periodType,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'payDate' => \Carbon\Carbon::parse($endDate)->addDays(5)->format('Y-m-d'),
            'status' => PayrollPeriodStatus::DRAFT,
            'totalGrossPay' => 0,
            'totalDeductions' => 0,
            'totalNetPay' => 0,
            'totalEmployerContributions' => 0,
            'employeeCount' => 0,
        ];
    }
}
