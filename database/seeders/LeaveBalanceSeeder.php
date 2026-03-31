<?php

namespace Database\Seeders;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveBalanceSeeder extends Seeder
{
    public function run(): void
    {
        $employees = Employee::all();
        $leaveTypes = LeaveType::where('isActive', true)->get();
        $year = 2026;

        foreach ($employees as $employee) {
            foreach ($leaveTypes as $leaveType) {
                // Skip gender-restricted leave types that don't match
                if ($leaveType->gender !== null && $leaveType->gender !== $employee->gender) {
                    continue;
                }

                LeaveBalance::create([
                    'employeeID' => $employee->employeeID,
                    'leaveTypeID' => $leaveType->id,
                    'year' => $year,
                    'totalCredits' => $leaveType->defaultCredits,
                    'usedCredits' => 0,
                    'pendingCredits' => 0,
                ]);
            }
        }
    }
}
