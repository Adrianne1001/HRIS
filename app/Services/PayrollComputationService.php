<?php

namespace App\Services;

use App\Enums\HolidayType;
use App\Enums\LeaveStatus;
use App\Enums\PayrollPeriodType;
use App\Models\AttendanceRecord;
use App\Models\DeductionType;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use Carbon\Carbon;

class PayrollComputationService
{
    /**
     * Compute payroll earnings for an employee in a given period.
     */
    public function computePayrollForEmployee(Employee $employee, PayrollPeriod $period): array
    {
        $schedule = $employee->workSchedule;
        $salary = (float) $employee->basicMonthlySalary;

        // Calculate standard working days per month from schedule
        $standardWorkingDaysPerMonth = $this->getStandardWorkingDaysPerMonth($schedule);

        // Rates
        $dailyRate = $salary / $standardWorkingDaysPerMonth;
        $hourlyRate = $dailyRate / (float) $schedule->totalWorkHours;

        // Basic pay (semi-monthly)
        $basicPay = $salary / 2;

        // Get attendance records in period
        $attendanceRecords = AttendanceRecord::where('employee_id', $employee->employeeID)
            ->whereBetween('workDate', [$period->startDate, $period->endDate])
            ->whereNotNull('actualTimeIn')
            ->get();

        // Get approved leaves overlapping with period
        $approvedLeaveDays = $this->getApprovedLeaveDays($employee, $period);

        // Get holidays in period
        $holidays = Holiday::whereBetween('date', [$period->startDate, $period->endDate])->get();
        $holidayTypes = $holidays->keyBy(fn ($h) => $h->date->format('Y-m-d'));

        // Working days in period
        $workingDaysInPeriod = $this->calculateWorkingDaysInPeriod(
            $period->startDate, $period->endDate, $schedule->workingDays
        );

        // Days worked (count of attendance records with actualTimeIn)
        $daysWorked = $attendanceRecords->count();

        // Days absent = working days - days worked - approved leave days (min 0)
        $daysAbsent = max(0, $workingDaysInPeriod - $daysWorked - $approvedLeaveDays);

        // Absent deduction
        $absentDeduction = round($dailyRate * $daysAbsent, 2);

        // Sum hours from attendance
        $regularHoursWorked = $attendanceRecords->sum(fn ($r) => (float) $r->hoursWorked);
        $overtimeHours = $attendanceRecords->sum(fn ($r) => (float) $r->advanceOTHours + (float) $r->afterShiftOTHours);

        // Late minutes: where actualTimeIn > shiftTimeIn
        $lateMinutes = 0;
        foreach ($attendanceRecords as $record) {
            if ($record->actualTimeIn && $record->shiftTimeIn) {
                $actual = Carbon::parse($record->actualTimeIn);
                $shift = Carbon::parse($record->shiftTimeIn);
                if ($actual->greaterThan($shift)) {
                    $lateMinutes += $shift->diffInMinutes($actual);
                }
            }
        }

        // Undertime minutes: where actualTimeOut < shiftTimeOut
        $undertimeMinutes = 0;
        foreach ($attendanceRecords as $record) {
            if ($record->actualTimeOut && $record->shiftTimeOut) {
                $actual = Carbon::parse($record->actualTimeOut);
                $shift = Carbon::parse($record->shiftTimeOut);
                if ($actual->lessThan($shift)) {
                    $undertimeMinutes += $actual->diffInMinutes($shift);
                }
            }
        }

        // Night differential hours
        $nightDifferentialHours = 0;
        foreach ($attendanceRecords as $record) {
            $nightDifferentialHours += $this->calculateNightDifferentialHours($record);
        }

        // Holiday days worked
        $holidayDaysWorked = 0;
        $specialHolidayDaysWorked = 0;
        foreach ($attendanceRecords as $record) {
            $dateStr = $record->workDate->format('Y-m-d');
            if (isset($holidayTypes[$dateStr])) {
                $holiday = $holidayTypes[$dateStr];
                if ($holiday->holidayType === HolidayType::REGULAR) {
                    $holidayDaysWorked++;
                } elseif ($holiday->holidayType === HolidayType::SPECIAL_NON_WORKING) {
                    $specialHolidayDaysWorked++;
                }
            }
        }

        // Compute pay components
        $overtimePay = round($hourlyRate * $overtimeHours * 0.25, 2);
        $nightDifferentialPay = round($hourlyRate * $nightDifferentialHours * 0.10, 2);
        $holidayPay = round($dailyRate * $holidayDaysWorked * 1.00, 2);
        $specialHolidayPay = round($dailyRate * $specialHolidayDaysWorked * 0.30, 2);
        $lateDeduction = round($hourlyRate * ($lateMinutes / 60), 2);
        $undertimeDeduction = round($hourlyRate * ($undertimeMinutes / 60), 2);

        $grossPay = round($basicPay - $absentDeduction - $lateDeduction - $undertimeDeduction
            + $overtimePay + $nightDifferentialPay + $holidayPay + $specialHolidayPay, 2);

        return [
            'basicMonthlySalary'     => $salary,
            'dailyRate'              => round($dailyRate, 2),
            'hourlyRate'             => round($hourlyRate, 2),
            'daysWorked'             => $daysWorked,
            'daysAbsent'             => $daysAbsent,
            'approvedLeaveDays'      => $approvedLeaveDays,
            'regularHoursWorked'     => round($regularHoursWorked, 2),
            'overtimeHours'          => round($overtimeHours, 2),
            'nightDifferentialHours' => round($nightDifferentialHours, 2),
            'holidayDaysWorked'      => $holidayDaysWorked,
            'specialHolidayDaysWorked' => $specialHolidayDaysWorked,
            'lateMinutes'            => round($lateMinutes, 2),
            'undertimeMinutes'       => round($undertimeMinutes, 2),
            'basicPay'               => round($basicPay, 2),
            'absentDeduction'        => $absentDeduction,
            'lateDeduction'          => $lateDeduction,
            'undertimeDeduction'     => $undertimeDeduction,
            'overtimePay'            => $overtimePay,
            'nightDifferentialPay'   => $nightDifferentialPay,
            'holidayPay'             => $holidayPay,
            'specialHolidayPay'      => $specialHolidayPay,
            'grossPay'               => $grossPay,
            'totalMandatoryDeductions' => 0,
            'totalLoanDeductions'    => 0,
            'totalDeductions'        => 0,
            'netPay'                 => $grossPay,
        ];
    }

    /**
     * Compute mandatory government deductions (only for Second Half periods).
     */
    public function computeMandatoryDeductions(PayrollRecord $record, PayrollPeriod $period): array
    {
        $zeros = [
            'sssEmployee'        => 0,
            'sssEmployer'        => 0,
            'sssEC'              => 0,
            'philhealthEmployee' => 0,
            'philhealthEmployer' => 0,
            'pagibigEmployee'    => 0,
            'pagibigEmployer'    => 0,
            'withholdingTax'     => 0,
        ];

        // Only apply on Second Half periods
        if ($period->periodType !== PayrollPeriodType::SECOND_HALF) {
            return $zeros;
        }

        $salary = (float) $record->basicMonthlySalary;

        // SSS
        $sss = $this->computeSSS($salary);

        // PhilHealth
        $philhealth = $this->computePhilHealth($salary);

        // Pag-IBIG
        $pagibig = $this->computePagibig($salary);

        // Withholding tax: taxable = grossPay - (SSS EE + PhilHealth EE + PagIBIG EE)
        $taxableIncome = (float) $record->grossPay
            - $sss['employeeShare']
            - $philhealth['employeeShare']
            - $pagibig['employeeShare'];

        $withholdingTax = $this->computeWithholdingTax($taxableIncome);

        return [
            'sssEmployee'        => $sss['employeeShare'],
            'sssEmployer'        => $sss['employerShare'],
            'sssEC'              => $sss['ec'],
            'philhealthEmployee' => $philhealth['employeeShare'],
            'philhealthEmployer' => $philhealth['employerShare'],
            'pagibigEmployee'    => $pagibig['employeeShare'],
            'pagibigEmployer'    => $pagibig['employerShare'],
            'withholdingTax'     => round($withholdingTax, 2),
        ];
    }

    /**
     * Compute loan deductions for an employee in a period.
     */
    public function computeLoanDeductions(Employee $employee, PayrollPeriod $period): array
    {
        $loans = EmployeeLoan::where('employeeID', $employee->employeeID)
            ->where('isActive', true)
            ->where('startDate', '<=', $period->endDate)
            ->get();

        $deductions = [];
        foreach ($loans as $loan) {
            $semiMonthly = round((float) $loan->monthlyAmortization / 2, 2);
            $remaining = (float) $loan->remainingBalance;
            $amount = min($semiMonthly, $remaining);
            if ($amount > 0) {
                $deductions[$loan->id] = round($amount, 2);
            }
        }

        return $deductions;
    }

    /**
     * Compute withholding tax using BIR semi-monthly tax brackets.
     */
    public function computeWithholdingTax(float $semiMonthlyTaxableIncome): float
    {
        $taxType = DeductionType::where('code', 'TAX')->first();
        if (!$taxType || !$taxType->bracketTable) {
            return 0;
        }

        $brackets = $taxType->bracketTable;
        $tax = 0;

        foreach ($brackets as $bracket) {
            $min = (float) $bracket['min'];
            $max = isset($bracket['max']) ? (float) $bracket['max'] : PHP_FLOAT_MAX;
            $base = (float) $bracket['base'];
            $rate = (float) $bracket['rate'];

            if ($semiMonthlyTaxableIncome > $min && ($bracket['max'] === null || $semiMonthlyTaxableIncome <= $max)) {
                $tax = $base + $rate * ($semiMonthlyTaxableIncome - $min);
                break;
            }
        }

        return max(0, round($tax, 2));
    }

    /**
     * Calculate night differential hours for an attendance record.
     * ND window: 10:00 PM to 6:00 AM next day.
     */
    public function calculateNightDifferentialHours(AttendanceRecord $record): float
    {
        if (!$record->actualTimeIn || !$record->actualTimeOut) {
            return 0;
        }

        $workStart = Carbon::parse($record->actualTimeIn);
        $workEnd = Carbon::parse($record->actualTimeOut);

        // Handle overnight shifts
        if ($workEnd->lessThanOrEqualTo($workStart)) {
            $workEnd->addDay();
        }

        // ND window for the work date: 10 PM that day to 6 AM next day
        $ndStart = $workStart->copy()->setTime(22, 0, 0);
        $ndEnd = $ndStart->copy()->addHours(8); // 6 AM next day

        // If work starts before 6 AM, the ND window could be from previous day
        if ($workStart->hour < 6) {
            $ndStart = $workStart->copy()->subDay()->setTime(22, 0, 0);
            $ndEnd = $workStart->copy()->setTime(6, 0, 0);
        }

        // Calculate overlap
        $overlapStart = $workStart->greaterThan($ndStart) ? $workStart : $ndStart;
        $overlapEnd = $workEnd->lessThan($ndEnd) ? $workEnd : $ndEnd;

        if ($overlapStart->greaterThanOrEqualTo($overlapEnd)) {
            // Check second ND window for shifts that span two ND windows
            $ndStart2 = $ndStart->copy()->addDay();
            $ndEnd2 = $ndEnd->copy()->addDay();
            $overlapStart2 = $workStart->greaterThan($ndStart2) ? $workStart : $ndStart2;
            $overlapEnd2 = $workEnd->lessThan($ndEnd2) ? $workEnd : $ndEnd2;

            if ($overlapStart2->greaterThanOrEqualTo($overlapEnd2)) {
                return 0;
            }

            return round($overlapStart2->diffInMinutes($overlapEnd2) / 60, 2);
        }

        $hours = $overlapStart->diffInMinutes($overlapEnd) / 60;

        // Also check if there's a second ND window overlap (very long shifts)
        $ndStart2 = $ndStart->copy()->addDay();
        $ndEnd2 = $ndEnd->copy()->addDay();
        if ($workEnd->greaterThan($ndStart2)) {
            $overlapStart2 = $workStart->greaterThan($ndStart2) ? $workStart : $ndStart2;
            $overlapEnd2 = $workEnd->lessThan($ndEnd2) ? $workEnd : $ndEnd2;
            if ($overlapStart2->lessThan($overlapEnd2)) {
                $hours += $overlapStart2->diffInMinutes($overlapEnd2) / 60;
            }
        }

        return round($hours, 2);
    }

    /**
     * Calculate working days in a period based on schedule.
     * dayMap matches Carbon's dayOfWeek: Sun=0, Mon=1, Tue=2, Wed=3, Thu=4, Fri=5, Sat=6.
     */
    public function calculateWorkingDaysInPeriod(Carbon $start, Carbon $end, string $workingDays): int
    {
        $dayMap = ['Mon' => 1, 'Tue' => 2, 'Wed' => 3, 'Thu' => 4, 'Fri' => 5, 'Sat' => 6, 'Sun' => 0];
        $days = explode(',', $workingDays);
        $workDayNumbers = array_map(fn ($d) => $dayMap[trim($d)] ?? -1, $days);

        $count = 0;
        $current = $start->copy();
        while ($current->lte($end)) {
            if (in_array($current->dayOfWeek, $workDayNumbers)) {
                $count++;
            }
            $current->addDay();
        }

        return $count;
    }

    // ---- Private Helpers ----

    private function getStandardWorkingDaysPerMonth($schedule): int
    {
        if (!$schedule || !$schedule->workingDays) {
            return 22; // default
        }

        $daysPerWeek = count(explode(',', $schedule->workingDays));
        // Standard approximations: 5-day = 22, 6-day = 26, 7-day = 30
        return match ($daysPerWeek) {
            6 => 26,
            7 => 30,
            default => 22,
        };
    }

    private function getApprovedLeaveDays(Employee $employee, PayrollPeriod $period): float
    {
        return (float) LeaveRequest::where('employeeID', $employee->employeeID)
            ->where('status', LeaveStatus::APPROVED)
            ->where(function ($q) use ($period) {
                $q->whereBetween('startDate', [$period->startDate, $period->endDate])
                  ->orWhereBetween('endDate', [$period->startDate, $period->endDate])
                  ->orWhere(function ($q2) use ($period) {
                      $q2->where('startDate', '<=', $period->startDate)
                         ->where('endDate', '>=', $period->endDate);
                  });
            })
            ->sum('totalDays');
    }

    private function computeSSS(float $monthlySalary): array
    {
        $sssType = DeductionType::where('code', 'SSS')->first();
        if (!$sssType || !$sssType->bracketTable) {
            return ['employeeShare' => 0, 'employerShare' => 0, 'ec' => 0];
        }

        $result = ['employeeShare' => 0, 'employerShare' => 0, 'ec' => 0];

        foreach ($sssType->bracketTable as $bracket) {
            $min = (float) $bracket['min'];
            $max = isset($bracket['max']) ? (float) $bracket['max'] : PHP_FLOAT_MAX;

            if ($monthlySalary >= $min && $monthlySalary <= $max) {
                $result['employeeShare'] = (float) $bracket['employeeShare'];
                $result['employerShare'] = (float) $bracket['employerShare'];
                $result['ec'] = (float) $bracket['ec'];
                break;
            }
        }

        return $result;
    }

    private function computePhilHealth(float $monthlySalary): array
    {
        $phType = DeductionType::where('code', 'PHIC')->first();
        if (!$phType) {
            return ['employeeShare' => 0, 'employerShare' => 0];
        }

        $floor = (float) ($phType->salaryFloor ?? 10000);
        $ceiling = (float) ($phType->salaryCeiling ?? 100000);
        $rate = (float) ($phType->employeeRate ?? 0.0250);

        $clamped = max($floor, min($ceiling, $monthlySalary));
        $share = round($clamped * $rate, 2);

        return ['employeeShare' => $share, 'employerShare' => $share];
    }

    private function computePagibig(float $monthlySalary): array
    {
        $hdmfType = DeductionType::where('code', 'HDMF')->first();
        if (!$hdmfType || !$hdmfType->bracketTable) {
            return ['employeeShare' => 0, 'employerShare' => 0];
        }

        $eeRate = 0.02;
        $erRate = 0.02;

        foreach ($hdmfType->bracketTable as $bracket) {
            $min = (float) $bracket['min'];
            $max = isset($bracket['max']) ? (float) $bracket['max'] : PHP_FLOAT_MAX;

            if ($monthlySalary >= $min && $monthlySalary <= $max) {
                $eeRate = (float) $bracket['employeeRate'];
                $erRate = (float) $bracket['employerRate'];
                break;
            }
        }

        $maxEE = (float) ($hdmfType->maxEmployeeAmount ?? 200);
        $maxER = (float) ($hdmfType->maxEmployerAmount ?? 200);

        $employeeShare = min(round($monthlySalary * $eeRate, 2), $maxEE);
        $employerShare = min(round($monthlySalary * $erRate, 2), $maxER);

        return ['employeeShare' => $employeeShare, 'employerShare' => $employerShare];
    }
}
