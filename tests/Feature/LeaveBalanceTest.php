<?php

use App\Enums\Gender;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;

test('leave balances index shows authenticated user balances', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['name' => 'Vacation Leave', 'code' => 'VL']);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 15,
    ]);

    $response = $this->actingAs($employee->user)->get('/leave-balances');

    $response->assertOk();
    $response->assertSee('Vacation Leave');
});

test('manage balances page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/leave-balances/manage');

    $response->assertOk();
});

test('bulk allocation creates balances for all employees', function () {
    $user = User::factory()->create();
    $employee = Employee::factory()->create(['gender' => Gender::MALE]);
    $leaveType = LeaveType::factory()->create([
        'code' => 'VL',
        'defaultCredits' => 15,
        'gender' => null,
        'isActive' => true,
    ]);

    $response = $this->actingAs($user)->post('/leave-balances/allocate', [
        'year' => 2026,
    ]);

    $response->assertRedirect();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('leave_balances', [
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => 2026,
        'totalCredits' => 15,
    ]);
});

test('bulk allocation respects gender eligibility', function () {
    $user = User::factory()->create();
    $maleEmployee = Employee::factory()->create(['gender' => Gender::MALE]);
    $femaleOnly = LeaveType::factory()->create([
        'code' => 'ML',
        'defaultCredits' => 105,
        'gender' => Gender::FEMALE,
        'isActive' => true,
    ]);

    $this->actingAs($user)->post('/leave-balances/allocate', [
        'year' => 2026,
    ]);

    $this->assertDatabaseMissing('leave_balances', [
        'employeeID' => $maleEmployee->employeeID,
        'leaveTypeID' => $femaleOnly->id,
    ]);
});

test('remaining credits are calculated correctly', function () {
    $balance = LeaveBalance::factory()->create([
        'totalCredits' => 15,
        'usedCredits' => 3,
        'pendingCredits' => 2,
    ]);

    expect($balance->remainingCredits)->toBe(10.0);
});
