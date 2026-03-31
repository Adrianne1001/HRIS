<?php

use App\Enums\LoanType;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\PayrollDeduction;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Models\User;

it('can view employee loans index', function () {
    $user = User::factory()->create();
    EmployeeLoan::factory()->count(3)->create();

    $response = $this->actingAs($user)->get(route('employee-loans.index'));

    $response->assertOk();
});

it('can create a loan with valid data', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create();

    $response = $this->actingAs($user)->post(route('employee-loans.store'), [
        'employeeID' => $employee->employeeID,
        'loanType' => LoanType::SSS_SALARY->value,
        'referenceNbr' => 'LN-12345678',
        'loanAmount' => 10000,
        'monthlyAmortization' => 500,
        'startDate' => '2026-01-01',
        'endDate' => '2027-08-31',
    ]);

    $response->assertRedirect(route('employee-loans.index'));
    $response->assertSessionHas('success');

    $this->assertDatabaseHas('employee_loans', [
        'employeeID' => $employee->employeeID,
        'loanType' => LoanType::SSS_SALARY->value,
        'loanAmount' => 10000,
        'remainingBalance' => 10000,
        'totalPaid' => 0,
        'isActive' => true,
    ]);
});

it('store validation fails for missing required fields', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post(route('employee-loans.store'), []);

    $response->assertSessionHasErrors(['employeeID', 'loanType', 'loanAmount', 'monthlyAmortization', 'startDate']);
});

it('can view loan show page', function () {
    $user = User::factory()->create();
    $loan = EmployeeLoan::factory()->create();

    $response = $this->actingAs($user)->get(route('employee-loans.show', $loan));

    $response->assertOk();
});

it('can view loan edit page', function () {
    $user = User::factory()->create();
    $loan = EmployeeLoan::factory()->create();

    $response = $this->actingAs($user)->get(route('employee-loans.edit', $loan));

    $response->assertOk();
});

it('can update a loan with valid data', function () {
    $user = User::factory()->create();
    $loan = EmployeeLoan::factory()->create([
        'loanAmount' => 10000,
        'monthlyAmortization' => 500,
        'totalPaid' => 1000,
        'remainingBalance' => 9000,
        'startDate' => '2026-01-01',
    ]);

    $response = $this->actingAs($user)->put(route('employee-loans.update', $loan), [
        'loanAmount' => 12000,
        'monthlyAmortization' => 600,
        'startDate' => '2026-01-01',
        'endDate' => '2028-01-01',
        'isActive' => true,
    ]);

    $response->assertRedirect(route('employee-loans.show', $loan));
    $response->assertSessionHas('success');

    // remainingBalance recalculated as loanAmount - totalPaid
    expect($loan->fresh()->loanAmount)->toEqual('12000.00');
    expect($loan->fresh()->monthlyAmortization)->toEqual('600.00');
    expect($loan->fresh()->remainingBalance)->toEqual('11000.00');
});

it('can delete a loan with no payroll deductions', function () {
    $user = User::factory()->create();
    $loan = EmployeeLoan::factory()->create();

    $response = $this->actingAs($user)->delete(route('employee-loans.destroy', $loan));

    $response->assertRedirect(route('employee-loans.index'));
    $this->assertDatabaseMissing('employee_loans', ['id' => $loan->id]);
});

it('cannot delete a loan with existing payroll deductions', function () {
    $user = User::factory()->create();

    $employee = Employee::factory()->create();
    $loan = EmployeeLoan::factory()->create([
        'employeeID' => $employee->employeeID,
    ]);

    $period = PayrollPeriod::factory()->create();
    $record = PayrollRecord::factory()->create([
        'payrollPeriodID' => $period->id,
        'employeeID' => $employee->employeeID,
    ]);

    PayrollDeduction::factory()->create([
        'payrollRecordID' => $record->id,
        'employeeLoanID' => $loan->id,
    ]);

    $response = $this->actingAs($user)->delete(route('employee-loans.destroy', $loan));

    $response->assertRedirect(route('employee-loans.show', $loan));
    $response->assertSessionHas('error');
    $this->assertDatabaseHas('employee_loans', ['id' => $loan->id]);
});

it('unauthenticated user is redirected to login for employee loans', function () {
    $response = $this->get(route('employee-loans.index'));

    $response->assertRedirect(route('login'));
});
