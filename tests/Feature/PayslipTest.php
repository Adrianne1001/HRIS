<?php

use App\Enums\PayrollPeriodStatus;
use App\Enums\PayrollPeriodType;
use App\Models\Employee;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\User;

it('employee can view own payslips index', function () {
    $employee = Employee::factory()->create();

    $completedPeriod = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::COMPLETED,
    ]);

    PayrollRecord::factory()->create([
        'payrollPeriodID' => $completedPeriod->id,
        'employeeID' => $employee->employeeID,
    ]);

    $response = $this->actingAs($employee->user)->get(route('payslips.index'));

    $response->assertOk();
});

it('payslips index only shows records from Completed periods', function () {
    $employee = Employee::factory()->create();

    // Record in a DRAFT period — should not appear
    $draftPeriod = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::DRAFT,
        'startDate' => '2026-03-01',
        'endDate' => '2026-03-15',
    ]);
    PayrollRecord::factory()->create([
        'payrollPeriodID' => $draftPeriod->id,
        'employeeID' => $employee->employeeID,
    ]);

    // Record in a PROCESSING period — should not appear
    $processingPeriod = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::PROCESSING,
        'startDate' => '2026-03-16',
        'endDate' => '2026-03-31',
    ]);
    PayrollRecord::factory()->create([
        'payrollPeriodID' => $processingPeriod->id,
        'employeeID' => $employee->employeeID,
    ]);

    // Record in a COMPLETED period — should appear
    $completedPeriod = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::COMPLETED,
        'startDate' => '2026-04-01',
        'endDate' => '2026-04-15',
    ]);
    PayrollRecord::factory()->create([
        'payrollPeriodID' => $completedPeriod->id,
        'employeeID' => $employee->employeeID,
    ]);

    $response = $this->actingAs($employee->user)->get(route('payslips.index'));

    $response->assertOk();
    $response->assertViewHas('payslips', function ($payslips) {
        return $payslips->total() === 1;
    });
});

it('employee can view own payslip show page with pay breakdown', function () {
    $employee = Employee::factory()->create();

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::COMPLETED,
    ]);

    $record = PayrollRecord::factory()->create([
        'payrollPeriodID' => $period->id,
        'employeeID' => $employee->employeeID,
    ]);

    $response = $this->actingAs($employee->user)->get(route('payslips.show', $record));

    $response->assertOk();
});

it('employee cannot view another employees payslip', function () {
    $employee1 = Employee::factory()->create();
    $employee2 = Employee::factory()->create();

    $period = PayrollPeriod::factory()->create([
        'status' => PayrollPeriodStatus::COMPLETED,
    ]);

    $recordForEmployee2 = PayrollRecord::factory()->create([
        'payrollPeriodID' => $period->id,
        'employeeID' => $employee2->employeeID,
    ]);

    $response = $this->actingAs($employee1->user)->get(route('payslips.show', $recordForEmployee2));

    $response->assertForbidden();
});

it('unauthenticated user is redirected to login for payslips', function () {
    $response = $this->get(route('payslips.index'));

    $response->assertRedirect(route('login'));
});
