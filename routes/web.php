<?php

use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkScheduleController;
use App\Http\Controllers\PayrollPeriodController;
use App\Http\Controllers\PayslipController;
use App\Http\Controllers\EmployeeLoanController;
use App\Http\Controllers\HolidayController;
use App\Http\Controllers\LeaveBalanceController;
use App\Http\Controllers\LeaveRequestController;
use App\Http\Controllers\LeaveTypeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Employee routes
    Route::resource('employees', EmployeeController::class);

    // Work Schedule routes
    Route::post('work-schedules/{workSchedule}/set-default', [WorkScheduleController::class, 'setDefault'])->name('work-schedules.set-default');
    Route::resource('work-schedules', WorkScheduleController::class);

    // Attendance / DTR routes
    Route::get('attendance/dtr', [AttendanceRecordController::class, 'dtr'])->name('attendance.dtr');
    Route::post('attendance/time-in', [AttendanceRecordController::class, 'timeIn'])->name('attendance.time-in');
    Route::post('attendance/time-out', [AttendanceRecordController::class, 'timeOut'])->name('attendance.time-out');
    Route::get('attendance/calendar', [AttendanceRecordController::class, 'calendar'])->name('attendance.calendar');
    Route::get('attendance', [AttendanceRecordController::class, 'index'])->name('attendance.index');
    Route::get('attendance/{attendanceRecord}', [AttendanceRecordController::class, 'show'])->name('attendance.show');

    // Leave Management routes
    Route::resource('leave-types', LeaveTypeController::class);

    Route::post('leave-requests/{leaveRequest}/approve', [LeaveRequestController::class, 'approve'])
        ->name('leave-requests.approve');
    Route::post('leave-requests/{leaveRequest}/reject', [LeaveRequestController::class, 'reject'])
        ->name('leave-requests.reject');
    Route::post('leave-requests/{leaveRequest}/cancel', [LeaveRequestController::class, 'cancel'])
        ->name('leave-requests.cancel');
    Route::resource('leave-requests', LeaveRequestController::class)->except(['edit', 'update', 'destroy']);

    Route::get('leave-balances', [LeaveBalanceController::class, 'index'])->name('leave-balances.index');
    Route::get('leave-balances/manage', [LeaveBalanceController::class, 'manage'])->name('leave-balances.manage');
    Route::post('leave-balances/allocate', [LeaveBalanceController::class, 'allocate'])->name('leave-balances.allocate');

    // Payroll Management
    Route::post('payroll/{payrollPeriod}/process', [PayrollPeriodController::class, 'process'])
        ->name('payroll.process');
    Route::post('payroll/{payrollPeriod}/complete', [PayrollPeriodController::class, 'complete'])
        ->name('payroll.complete');
    Route::resource('payroll', PayrollPeriodController::class)->except(['edit', 'update'])->parameters(['payroll' => 'payrollPeriod']);

    // Payslips (employee self-service)
    Route::get('payslips', [PayslipController::class, 'index'])->name('payslips.index');
    Route::get('payslips/{payrollRecord}', [PayslipController::class, 'show'])->name('payslips.show');

    // Employee Loans
    Route::resource('employee-loans', EmployeeLoanController::class);

    // Holidays
    Route::resource('holidays', HolidayController::class)->except(['show']);
});

require __DIR__.'/auth.php';
