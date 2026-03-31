<?php

use App\Enums\LeaveStatus;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;

test('leave requests index page is displayed', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/leave-requests');

    $response->assertOk();
});

test('leave request create page is displayed for employee', function () {
    $employee = Employee::factory()->create();

    $response = $this->actingAs($employee->user)->get('/leave-requests/create');

    $response->assertOk();
});

test('leave request can be submitted with sufficient balance', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL', 'name' => 'Vacation Leave', 'maxConsecutiveDays' => null]);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 15,
        'usedCredits' => 0,
        'pendingCredits' => 0,
    ]);

    // Use a Monday to Friday range (5 business days) in the future
    $startDate = now()->next('Monday')->format('Y-m-d');
    $endDate = now()->next('Monday')->addDays(4)->format('Y-m-d');

    $response = $this->actingAs($employee->user)->post('/leave-requests', [
        'leaveTypeID' => $leaveType->id,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'isHalfDay' => false,
        'reason' => 'Family vacation',
    ]);

    $response->assertRedirect('/leave-requests');
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('leave_requests', [
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'status' => 'Pending',
    ]);
});

test('leave request is rejected when balance is insufficient', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL']);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 1,
        'usedCredits' => 0,
        'pendingCredits' => 0,
    ]);

    $startDate = now()->next('Monday')->format('Y-m-d');
    $endDate = now()->next('Monday')->addDays(4)->format('Y-m-d');

    $response = $this->actingAs($employee->user)->post('/leave-requests', [
        'leaveTypeID' => $leaveType->id,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'isHalfDay' => false,
        'reason' => 'Need time off',
    ]);

    $response->assertSessionHasErrors(['leaveTypeID']);
});

test('overlapping leave requests are prevented', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL']);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 30,
        'usedCredits' => 0,
        'pendingCredits' => 0,
    ]);

    $startDate = now()->next('Monday')->format('Y-m-d');
    $endDate = now()->next('Monday')->addDays(4)->format('Y-m-d');

    // First request
    $this->actingAs($employee->user)->post('/leave-requests', [
        'leaveTypeID' => $leaveType->id,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'isHalfDay' => false,
        'reason' => 'First request',
    ]);

    // Overlapping request
    $response = $this->actingAs($employee->user)->post('/leave-requests', [
        'leaveTypeID' => $leaveType->id,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'isHalfDay' => false,
        'reason' => 'Overlapping request',
    ]);

    $response->assertSessionHasErrors(['startDate']);
});

test('half-day leave request is stored as 0.5 days', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL']);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 15,
        'usedCredits' => 0,
        'pendingCredits' => 0,
    ]);

    $date = now()->next('Monday')->format('Y-m-d');

    $response = $this->actingAs($employee->user)->post('/leave-requests', [
        'leaveTypeID' => $leaveType->id,
        'startDate' => $date,
        'endDate' => $date,
        'isHalfDay' => true,
        'halfDayPeriod' => 'AM',
        'reason' => 'Morning appointment',
    ]);

    $response->assertRedirect('/leave-requests');
    $this->assertDatabaseHas('leave_requests', [
        'employeeID' => $employee->employeeID,
        'totalDays' => 0.5,
        'isHalfDay' => true,
        'halfDayPeriod' => 'AM',
    ]);
});

test('leave request can be approved', function () {
    $admin = User::factory()->create();
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL']);

    $balance = LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 15,
        'usedCredits' => 0,
        'pendingCredits' => 2,
    ]);

    $leaveRequest = LeaveRequest::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'startDate' => now()->next('Monday'),
        'endDate' => now()->next('Monday')->addDay(),
        'totalDays' => 2,
        'status' => LeaveStatus::PENDING,
    ]);

    $response = $this->actingAs($admin)->post("/leave-requests/{$leaveRequest->id}/approve");

    $response->assertRedirect("/leave-requests/{$leaveRequest->id}");
    $response->assertSessionHas('success');

    $leaveRequest->refresh();
    expect($leaveRequest->status)->toBe(LeaveStatus::APPROVED);
    expect($leaveRequest->approvedByID)->toBe($admin->id);

    $balance->refresh();
    expect((float)$balance->usedCredits)->toBe(2.0);
    expect((float)$balance->pendingCredits)->toBe(0.0);
});

test('approved leave creates attendance records', function () {
    $admin = User::factory()->create();
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL']);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 15,
        'usedCredits' => 0,
        'pendingCredits' => 1,
    ]);

    $monday = now()->next('Monday');

    $leaveRequest = LeaveRequest::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'startDate' => $monday,
        'endDate' => $monday,
        'totalDays' => 1,
        'status' => LeaveStatus::PENDING,
    ]);

    $this->actingAs($admin)->post("/leave-requests/{$leaveRequest->id}/approve");

    $this->assertDatabaseHas('attendance_records', [
        'employee_id' => $employee->employeeID,
        'workDate' => $monday->startOfDay()->toDateTimeString(),
        'remarks' => 'Vacation Leave',
    ]);
});

test('leave request can be rejected with reason', function () {
    $admin = User::factory()->create();
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL']);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 15,
        'usedCredits' => 0,
        'pendingCredits' => 1,
    ]);

    $leaveRequest = LeaveRequest::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'totalDays' => 1,
        'status' => LeaveStatus::PENDING,
    ]);

    $response = $this->actingAs($admin)->post("/leave-requests/{$leaveRequest->id}/reject", [
        'rejectionReason' => 'Insufficient staffing on those dates.',
    ]);

    $response->assertRedirect("/leave-requests/{$leaveRequest->id}");
    $leaveRequest->refresh();
    expect($leaveRequest->status)->toBe(LeaveStatus::REJECTED);
    expect($leaveRequest->rejectionReason)->toBe('Insufficient staffing on those dates.');
});

test('leave request can be cancelled', function () {
    $employee = Employee::factory()->create();
    $leaveType = LeaveType::factory()->create(['code' => 'VL']);

    LeaveBalance::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'year' => now()->year,
        'totalCredits' => 15,
        'usedCredits' => 0,
        'pendingCredits' => 1,
    ]);

    $leaveRequest = LeaveRequest::factory()->create([
        'employeeID' => $employee->employeeID,
        'leaveTypeID' => $leaveType->id,
        'totalDays' => 1,
        'status' => LeaveStatus::PENDING,
    ]);

    $response = $this->actingAs($employee->user)->post("/leave-requests/{$leaveRequest->id}/cancel");

    $response->assertRedirect('/leave-requests');
    $leaveRequest->refresh();
    expect($leaveRequest->status)->toBe(LeaveStatus::CANCELLED);
});

test('only pending requests can be approved', function () {
    $admin = User::factory()->create();
    $leaveRequest = LeaveRequest::factory()->create([
        'status' => LeaveStatus::APPROVED,
    ]);

    $response = $this->actingAs($admin)->post("/leave-requests/{$leaveRequest->id}/approve");

    $response->assertSessionHas('error');
});

test('rejection requires a reason', function () {
    $admin = User::factory()->create();
    $leaveRequest = LeaveRequest::factory()->create([
        'status' => LeaveStatus::PENDING,
    ]);

    $response = $this->actingAs($admin)->post("/leave-requests/{$leaveRequest->id}/reject", [
        'rejectionReason' => '',
    ]);

    $response->assertSessionHasErrors(['rejectionReason']);
});
