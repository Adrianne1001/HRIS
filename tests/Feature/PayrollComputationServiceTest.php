<?php

use App\Enums\EmploymentStatus;
use App\Enums\HolidayType;
use App\Enums\LeaveStatus;
use App\Enums\PayrollPeriodType;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\Holiday;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\WorkSchedule;
use App\Services\PayrollComputationService;
use Carbon\Carbon;
use Database\Seeders\DeductionTypeSeeder;

beforeEach(function () {
    $this->seed(DeductionTypeSeeder::class);

    $this->schedule = WorkSchedule::factory()->create([
        'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
        'totalWorkHours' => 8,
    ]);

    $this->employee = Employee::factory()->create([
        'workScheduleID' => $this->schedule->id,
        'basicMonthlySalary' => 22000,
        'employmentStatus' => EmploymentStatus::ACTIVE,
    ]);

    // First half: Apr 1-15, 2026 has 11 Mon-Fri working days
    $this->period = PayrollPeriod::factory()->create([
        'periodType' => PayrollPeriodType::FIRST_HALF,
        'startDate' => '2026-04-01',
        'endDate' => '2026-04-15',
    ]);

    $this->service = app(PayrollComputationService::class);
});

it('calculateWorkingDaysInPeriod counts Mon-Fri working days in first half of April 2026', function () {
    $result = $this->service->calculateWorkingDaysInPeriod(
        Carbon::parse('2026-04-01'),
        Carbon::parse('2026-04-15'),
        'Mon,Tue,Wed,Thu,Fri'
    );

    expect($result)->toBe(11);
});

it('computePayrollForEmployee calculates correct daily rate', function () {
    // salary 22000, 5-day schedule → monthly divisor 22 → daily rate = 1000.00
    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    expect($earnings['dailyRate'])->toEqual(1000.00);
});

it('computePayrollForEmployee calculates correct hourly rate', function () {
    // daily rate 1000 ÷ 8 hours = 125.00
    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    expect($earnings['hourlyRate'])->toEqual(125.00);
});

it('computePayrollForEmployee calculates correct basic pay for semi-monthly period', function () {
    // basicPay = salary / 2 = 22000 / 2 = 11000.00
    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    expect($earnings['basicPay'])->toEqual(11000.00);
});

it('computePayrollForEmployee counts all working days as absent when no attendance records exist', function () {
    // 11 working days in period, 0 attended → daysAbsent = 11
    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    expect($earnings['daysAbsent'])->toEqual(11);
    expect($earnings['absentDeduction'])->toEqual(11000.00);
});

it('approved leave days reduce absent deductions', function () {
    $leaveType = LeaveType::factory()->create();

    LeaveRequest::factory()->create([
        'employeeID' => $this->employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'status' => LeaveStatus::APPROVED,
        'startDate' => '2026-04-06',
        'endDate' => '2026-04-08',
        'totalDays' => 3,
    ]);

    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    // working days = 11, attended = 0, approved leave = 3 → absent = 8
    expect((float) $earnings['approvedLeaveDays'])->toEqual(3.0);
    expect($earnings['daysAbsent'])->toEqual(8);
});

it('computePayrollForEmployee computes overtime pay correctly', function () {
    AttendanceRecord::create([
        'employee_id' => $this->employee->employeeID,
        'workDate' => '2026-04-07',
        'shiftTimeIn' => '2026-04-07 09:00:00',
        'shiftTimeOut' => '2026-04-07 17:00:00',
        'actualTimeIn' => '2026-04-07 09:00:00',
        'actualTimeOut' => '2026-04-07 19:00:00',
        'hoursWorked' => 8,
        'advanceOTHours' => 0,
        'afterShiftOTHours' => 2,
    ]);

    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    // overtimePay = hourlyRate × otHours × 0.25 = 125 × 2 × 0.25 = 62.50
    expect($earnings['overtimePay'])->toEqual(62.50);
});

it('computePayrollForEmployee computes night differential pay correctly', function () {
    // Shift entirely within ND window (10 PM – 6 AM)
    AttendanceRecord::create([
        'employee_id' => $this->employee->employeeID,
        'workDate' => '2026-04-07',
        'shiftTimeIn' => '2026-04-07 22:00:00',
        'shiftTimeOut' => '2026-04-08 06:00:00',
        'actualTimeIn' => '2026-04-07 22:00:00',
        'actualTimeOut' => '2026-04-08 06:00:00',
        'hoursWorked' => 8,
        'advanceOTHours' => 0,
        'afterShiftOTHours' => 0,
    ]);

    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    // nightDifferentialPay = hourlyRate × 8 × 0.10 = 125 × 8 × 0.10 = 100.00
    expect($earnings['nightDifferentialPay'])->toEqual(100.00);
});

it('computePayrollForEmployee computes regular holiday pay correctly', function () {
    Holiday::create([
        'name' => 'Araw ng Kagitingan',
        'date' => '2026-04-06',
        'holidayType' => HolidayType::REGULAR,
        'year' => 2026,
    ]);

    AttendanceRecord::create([
        'employee_id' => $this->employee->employeeID,
        'workDate' => '2026-04-06',
        'shiftTimeIn' => '2026-04-06 09:00:00',
        'shiftTimeOut' => '2026-04-06 17:00:00',
        'actualTimeIn' => '2026-04-06 09:00:00',
        'actualTimeOut' => '2026-04-06 17:00:00',
        'hoursWorked' => 8,
        'advanceOTHours' => 0,
        'afterShiftOTHours' => 0,
    ]);

    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    // holidayPay = dailyRate × 1.0 × holidayDays = 1000 × 1.0 × 1 = 1000.00
    expect($earnings['holidayPay'])->toEqual(1000.00);
});

it('computePayrollForEmployee computes special holiday pay correctly', function () {
    Holiday::create([
        'name' => 'Special Working Day',
        'date' => '2026-04-07',
        'holidayType' => HolidayType::SPECIAL_NON_WORKING,
        'year' => 2026,
    ]);

    AttendanceRecord::create([
        'employee_id' => $this->employee->employeeID,
        'workDate' => '2026-04-07',
        'shiftTimeIn' => '2026-04-07 09:00:00',
        'shiftTimeOut' => '2026-04-07 17:00:00',
        'actualTimeIn' => '2026-04-07 09:00:00',
        'actualTimeOut' => '2026-04-07 17:00:00',
        'hoursWorked' => 8,
        'advanceOTHours' => 0,
        'afterShiftOTHours' => 0,
    ]);

    $earnings = $this->service->computePayrollForEmployee($this->employee, $this->period);

    // specialHolidayPay = dailyRate × 0.30 × days = 1000 × 0.30 × 1 = 300.00
    expect($earnings['specialHolidayPay'])->toEqual(300.00);
});

it('computeMandatoryDeductions returns zeros for First Half period', function () {
    $record = PayrollRecord::factory()->create([
        'payrollPeriodID' => $this->period->id,
        'employeeID' => $this->employee->employeeID,
        'basicMonthlySalary' => 22000,
        'grossPay' => 11000,
    ]);

    $result = $this->service->computeMandatoryDeductions($record, $this->period);

    expect($result['sssEmployee'])->toBe(0);
    expect($result['philhealthEmployee'])->toBe(0);
    expect($result['pagibigEmployee'])->toBe(0);
    expect($result['withholdingTax'])->toBe(0);
    expect($result['sssEmployer'])->toBe(0);
});

it('computeMandatoryDeductions computes SSS employee share from bracket for Second Half period', function () {
    $secondHalfPeriod = PayrollPeriod::factory()->create([
        'periodType' => PayrollPeriodType::SECOND_HALF,
        'startDate' => '2026-04-16',
        'endDate' => '2026-04-30',
    ]);

    // salary 15000 → SSS bracket [14750, 15249.99] → employeeShare = 675.00
    $record = PayrollRecord::factory()->create([
        'payrollPeriodID' => $secondHalfPeriod->id,
        'employeeID' => $this->employee->employeeID,
        'basicMonthlySalary' => 15000,
        'grossPay' => 7500,
    ]);

    $result = $this->service->computeMandatoryDeductions($record, $secondHalfPeriod);

    expect($result['sssEmployee'])->toEqual(675.00);
});

it('computeMandatoryDeductions computes PhilHealth employee share for Second Half period', function () {
    $secondHalfPeriod = PayrollPeriod::factory()->create([
        'periodType' => PayrollPeriodType::SECOND_HALF,
        'startDate' => '2026-04-16',
        'endDate' => '2026-04-30',
    ]);

    // salary 15000, rate 2.5%, floor 10000, ceiling 100000
    // share = 15000 × 0.025 = 375.00
    $record = PayrollRecord::factory()->create([
        'payrollPeriodID' => $secondHalfPeriod->id,
        'employeeID' => $this->employee->employeeID,
        'basicMonthlySalary' => 15000,
        'grossPay' => 7500,
    ]);

    $result = $this->service->computeMandatoryDeductions($record, $secondHalfPeriod);

    expect($result['philhealthEmployee'])->toEqual(375.00);
    expect($result['philhealthEmployer'])->toEqual(375.00);
});

it('computeMandatoryDeductions computes Pag-IBIG employee share capped at maximum', function () {
    $secondHalfPeriod = PayrollPeriod::factory()->create([
        'periodType' => PayrollPeriodType::SECOND_HALF,
        'startDate' => '2026-04-16',
        'endDate' => '2026-04-30',
    ]);

    // salary 30000, rate 2% → 30000 × 0.02 = 600, capped at max 200
    $employee = Employee::factory()->create([
        'workScheduleID' => $this->schedule->id,
        'basicMonthlySalary' => 30000,
    ]);

    $record = PayrollRecord::factory()->create([
        'payrollPeriodID' => $secondHalfPeriod->id,
        'employeeID' => $employee->employeeID,
        'basicMonthlySalary' => 30000,
        'grossPay' => 15000,
    ]);

    $result = $this->service->computeMandatoryDeductions($record, $secondHalfPeriod);

    expect($result['pagibigEmployee'])->toEqual(200.00);
    expect($result['pagibigEmployer'])->toEqual(200.00);
});

it('computeWithholdingTax returns zero for income at or below exemption threshold', function () {
    // bracket[0]: min=0, max=10417, base=0, rate=0
    // 10417 > 0 and 10417 <= 10417 → tax = 0
    $result = $this->service->computeWithholdingTax(10417.00);

    expect($result)->toEqual(0.0);
});

it('computeWithholdingTax computes correctly in the 15% bracket', function () {
    // bracket[1]: min=10417, max=16667, base=0, rate=0.15
    // income=16667 → tax = 0 + 0.15 × (16667 - 10417) = 0.15 × 6250 = 937.50
    $result = $this->service->computeWithholdingTax(16667.00);

    expect($result)->toEqual(937.50);
});

it('computeLoanDeductions deducts semi-monthly amortization from active loan', function () {
    $loan = EmployeeLoan::factory()->create([
        'employeeID' => $this->employee->employeeID,
        'monthlyAmortization' => 1000,
        'remainingBalance' => 5000,
        'isActive' => true,
        'startDate' => '2026-01-01',
    ]);

    $result = $this->service->computeLoanDeductions($this->employee, $this->period);

    // semiMonthly = round(1000 / 2, 2) = 500
    expect($result[$loan->id])->toEqual(500.00);
});

it('computeLoanDeductions caps deduction at remaining balance', function () {
    $loan = EmployeeLoan::factory()->create([
        'employeeID' => $this->employee->employeeID,
        'monthlyAmortization' => 1000,
        'remainingBalance' => 300,
        'isActive' => true,
        'startDate' => '2026-01-01',
    ]);

    $result = $this->service->computeLoanDeductions($this->employee, $this->period);

    // semiMonthly = 500, but remaining = 300 → capped at 300
    expect($result[$loan->id])->toEqual(300.00);
});

it('computeLoanDeductions excludes inactive loans', function () {
    EmployeeLoan::factory()->create([
        'employeeID' => $this->employee->employeeID,
        'monthlyAmortization' => 1000,
        'remainingBalance' => 5000,
        'isActive' => false,
        'startDate' => '2026-01-01',
    ]);

    $result = $this->service->computeLoanDeductions($this->employee, $this->period);

    expect($result)->toBeEmpty();
});
