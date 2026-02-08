<?php

namespace Database\Seeders;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceRecordSeeder extends Seeder
{
    /**
     * Day name abbreviations used in workingDays field.
     */
    private array $dayMap = [
        0 => 'Sun',
        1 => 'Mon',
        2 => 'Tue',
        3 => 'Wed',
        4 => 'Thu',
        5 => 'Fri',
        6 => 'Sat',
    ];

    /**
     * Seed attendance records for all active employees.
     */
    public function run(): void
    {
        $employees = Employee::with('workSchedule')
            ->where('employmentStatus', 'Active')
            ->get();

        if ($employees->isEmpty()) {
            $this->command->warn('No active employees found. Please run EmployeeSeeder first.');
            return;
        }

        $recordCount = 0;
        $daysToGenerate = 30; // Generate attendance for the past 30 days
        $today = Carbon::today();

        foreach ($employees as $employee) {
            if (!$employee->workSchedule) {
                $this->command->warn("Employee {$employee->employeeID} has no work schedule assigned. Skipping.");
                continue;
            }

            $workingDays = explode(',', $employee->workSchedule->workingDays);
            
            // Generate attendance for each day
            for ($i = $daysToGenerate; $i >= 1; $i--) {
                $date = $today->copy()->subDays($i);
                $dayAbbrev = $this->dayMap[$date->dayOfWeek];

                // Skip non-working days
                if (!in_array($dayAbbrev, $workingDays)) {
                    continue;
                }

                // Skip if employee wasn't hired yet
                if ($date->lt($employee->hireDate)) {
                    continue;
                }

                $record = $this->generateAttendanceRecord($employee, $date);
                if ($record) {
                    $recordCount++;
                }
            }
        }

        $this->command->info("Created {$recordCount} attendance records successfully.");
    }

    /**
     * Generate a single attendance record with realistic variations.
     */
    private function generateAttendanceRecord(Employee $employee, Carbon $date): ?AttendanceRecord
    {
        $schedule = $employee->workSchedule;
        
        // Parse schedule times
        $startTime = Carbon::parse($schedule->startTime);
        $endTime = Carbon::parse($schedule->endTime);
        
        // Build full datetime for shift
        $shiftTimeIn = $date->copy()->setTime($startTime->hour, $startTime->minute, 0);
        $shiftTimeOut = $date->copy()->setTime($endTime->hour, $endTime->minute, 0);
        
        // Handle overnight shifts (e.g., 22:00 - 06:00)
        if ($shiftTimeOut->lessThanOrEqualTo($shiftTimeIn)) {
            $shiftTimeOut->addDay();
        }

        // Determine attendance scenario (weighted probabilities)
        $scenario = $this->determineScenario();

        // Initialize record data
        $recordData = [
            'employee_id' => $employee->employeeID,
            'workDate' => $date->toDateString(),
            'shiftTimeIn' => $shiftTimeIn,
            'shiftTimeOut' => $shiftTimeOut,
            'actualTimeIn' => null,
            'actualTimeOut' => null,
            'hoursWorked' => 0,
            'advanceOTHours' => 0,
            'afterShiftOTHours' => 0,
            'inOrOut' => null,
            'remarks' => null,
        ];

        switch ($scenario) {
            case 'on_time':
                $recordData = $this->generateOnTimeAttendance($recordData, $shiftTimeIn, $shiftTimeOut, $schedule);
                break;
            
            case 'early_arrival':
                $recordData = $this->generateEarlyArrivalAttendance($recordData, $shiftTimeIn, $shiftTimeOut, $schedule);
                break;
            
            case 'late':
                $recordData = $this->generateLateAttendance($recordData, $shiftTimeIn, $shiftTimeOut, $schedule);
                break;
            
            case 'undertime':
                $recordData = $this->generateUndertimeAttendance($recordData, $shiftTimeIn, $shiftTimeOut, $schedule);
                break;
            
            case 'overtime':
                $recordData = $this->generateOvertimeAttendance($recordData, $shiftTimeIn, $shiftTimeOut, $schedule);
                break;
            
            case 'vacation_leave':
                $recordData['remarks'] = AttendanceRecord::REMARKS_VACATION_LEAVE;
                break;
            
            case 'sick_leave':
                $recordData['remarks'] = AttendanceRecord::REMARKS_SICK_LEAVE;
                break;
            
            case 'no_time_in':
                $recordData['remarks'] = AttendanceRecord::REMARKS_NO_TIME_IN;
                // They timed out but didn't time in (data correction needed)
                $recordData['actualTimeOut'] = $shiftTimeOut->copy()->addMinutes(fake()->numberBetween(-10, 5));
                $recordData['inOrOut'] = AttendanceRecord::OUT;
                break;
            
            case 'no_time_out':
                $recordData['actualTimeIn'] = $shiftTimeIn->copy()->addMinutes(fake()->numberBetween(-5, 10));
                $recordData['inOrOut'] = AttendanceRecord::IN;
                $recordData['remarks'] = AttendanceRecord::REMARKS_NO_TIME_OUT;
                break;
        }

        // Set inOrOut based on presence of times (if not already set)
        if ($recordData['actualTimeIn'] && $recordData['actualTimeOut'] && !$recordData['inOrOut']) {
            $recordData['inOrOut'] = AttendanceRecord::OUT;
        } elseif ($recordData['actualTimeIn'] && !$recordData['actualTimeOut'] && !$recordData['inOrOut']) {
            $recordData['inOrOut'] = AttendanceRecord::IN;
        }

        return AttendanceRecord::create($recordData);
    }

    /**
     * Determine attendance scenario with weighted probabilities.
     */
    private function determineScenario(): string
    {
        $rand = fake()->numberBetween(1, 100);

        // Weighted distribution:
        // 55% on time
        // 12% early arrival (potential advance OT)
        // 12% late
        // 5% undertime
        // 8% overtime (after shift)
        // 3% vacation leave
        // 2% sick leave
        // 2% no time out (forgot to clock out)
        // 1% no time in (forgot to clock in)

        if ($rand <= 55) return 'on_time';
        if ($rand <= 67) return 'early_arrival';
        if ($rand <= 79) return 'late';
        if ($rand <= 84) return 'undertime';
        if ($rand <= 92) return 'overtime';
        if ($rand <= 95) return 'vacation_leave';
        if ($rand <= 97) return 'sick_leave';
        if ($rand <= 99) return 'no_time_out';
        return 'no_time_in';
    }

    /**
     * Generate on-time attendance (within ±5 minutes of shift).
     */
    private function generateOnTimeAttendance(array $data, Carbon $shiftIn, Carbon $shiftOut, $schedule): array
    {
        // Arrive within -5 to +5 minutes of shift start
        $timeIn = $shiftIn->copy()->addMinutes(fake()->numberBetween(-5, 5));
        // Leave within -5 to +5 minutes of shift end
        $timeOut = $shiftOut->copy()->addMinutes(fake()->numberBetween(-5, 5));

        $data['actualTimeIn'] = $timeIn;
        $data['actualTimeOut'] = $timeOut;
        $data['hoursWorked'] = $this->calculateHoursWorked($timeIn, $timeOut, $shiftIn, $shiftOut, $schedule);
        
        return $data;
    }

    /**
     * Generate early arrival attendance (advance OT potential).
     */
    private function generateEarlyArrivalAttendance(array $data, Carbon $shiftIn, Carbon $shiftOut, $schedule): array
    {
        // Arrive 15-60 minutes early
        $earlyMinutes = fake()->numberBetween(15, 60);
        $timeIn = $shiftIn->copy()->subMinutes($earlyMinutes);
        // Leave on time or slightly after
        $timeOut = $shiftOut->copy()->addMinutes(fake()->numberBetween(-5, 10));

        $data['actualTimeIn'] = $timeIn;
        $data['actualTimeOut'] = $timeOut;
        $data['advanceOTHours'] = round($earlyMinutes / 60, 2);
        $data['hoursWorked'] = $this->calculateHoursWorked($timeIn, $timeOut, $shiftIn, $shiftOut, $schedule);

        return $data;
    }

    /**
     * Generate late attendance.
     */
    private function generateLateAttendance(array $data, Carbon $shiftIn, Carbon $shiftOut, $schedule): array
    {
        // Arrive 6-45 minutes late
        $lateMinutes = fake()->numberBetween(6, 45);
        $timeIn = $shiftIn->copy()->addMinutes($lateMinutes);
        // Leave on time or slightly after
        $timeOut = $shiftOut->copy()->addMinutes(fake()->numberBetween(-5, 15));

        $data['actualTimeIn'] = $timeIn;
        $data['actualTimeOut'] = $timeOut;
        $data['hoursWorked'] = $this->calculateHoursWorked($timeIn, $timeOut, $shiftIn, $shiftOut, $schedule);
        $data['remarks'] = AttendanceRecord::REMARKS_LATE;

        return $data;
    }

    /**
     * Generate undertime attendance (left early).
     */
    private function generateUndertimeAttendance(array $data, Carbon $shiftIn, Carbon $shiftOut, $schedule): array
    {
        // Arrive on time or slightly late
        $timeIn = $shiftIn->copy()->addMinutes(fake()->numberBetween(-5, 10));
        // Leave 15-60 minutes early
        $earlyLeaveMinutes = fake()->numberBetween(15, 60);
        $timeOut = $shiftOut->copy()->subMinutes($earlyLeaveMinutes);

        $data['actualTimeIn'] = $timeIn;
        $data['actualTimeOut'] = $timeOut;
        $data['hoursWorked'] = $this->calculateHoursWorked($timeIn, $timeOut, $shiftIn, $shiftOut, $schedule);
        $data['remarks'] = AttendanceRecord::REMARKS_UNDERTIME;

        return $data;
    }

    /**
     * Generate overtime attendance (stayed late).
     */
    private function generateOvertimeAttendance(array $data, Carbon $shiftIn, Carbon $shiftOut, $schedule): array
    {
        // Arrive on time or early
        $timeIn = $shiftIn->copy()->addMinutes(fake()->numberBetween(-10, 5));
        // Leave 30-180 minutes late (0.5 to 3 hours OT)
        $overtimeMinutes = fake()->numberBetween(30, 180);
        $timeOut = $shiftOut->copy()->addMinutes($overtimeMinutes);

        $data['actualTimeIn'] = $timeIn;
        $data['actualTimeOut'] = $timeOut;
        $data['afterShiftOTHours'] = round($overtimeMinutes / 60, 2);
        $data['hoursWorked'] = $this->calculateHoursWorked($timeIn, $timeOut, $shiftIn, $shiftOut, $schedule);

        // Might also have advance OT if arrived early
        if ($timeIn->lessThan($shiftIn)) {
            $data['advanceOTHours'] = round($shiftIn->diffInMinutes($timeIn) / 60, 2);
        }

        return $data;
    }

    /**
     * Calculate hours worked (capped at schedule's totalWorkHours).
     */
    private function calculateHoursWorked(Carbon $timeIn, Carbon $timeOut, Carbon $shiftIn, Carbon $shiftOut, $schedule): float
    {
        // Use the later of actualTimeIn or shiftTimeIn as effective start
        $effectiveStart = $timeIn->greaterThan($shiftIn) ? $timeIn : $shiftIn;
        
        // Use the earlier of actualTimeOut or shiftTimeOut as effective end
        $effectiveEnd = $timeOut->lessThan($shiftOut) ? $timeOut : $shiftOut;

        // If effective end is before effective start, return 0
        if ($effectiveEnd->lessThanOrEqualTo($effectiveStart)) {
            return 0;
        }

        $workedMinutes = $effectiveStart->diffInMinutes($effectiveEnd);
        $hoursWorked = round($workedMinutes / 60, 2);

        // Cap at schedule's totalWorkHours
        $maxHours = (float) $schedule->totalWorkHours;

        return min($hoursWorked, $maxHours);
    }
}
