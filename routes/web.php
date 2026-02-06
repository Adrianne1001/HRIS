<?php

use App\Http\Controllers\AttendanceRecordController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WorkScheduleController;
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
});

require __DIR__.'/auth.php';
