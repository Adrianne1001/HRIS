<?php

namespace Database\Seeders;

use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;

class WorkScheduleSeeder extends Seeder
{
    /**
     * Seed the work schedules table with realistic shift configurations.
     */
    public function run(): void
    {
        $schedules = [
            [
                'name' => 'Day Shift (Regular)',
                'startTime' => '08:00',
                'endTime' => '17:00',
                'startBreakTime' => '12:00',
                'endBreakTime' => '13:00',
                'workingDays' => 'Mon,Tue,Wed,Thu,Fri',
                'totalWorkHours' => 8.00,
                'isDefault' => true,
            ],
            [
                'name' => 'Day Shift (6-Day)',
                'startTime' => '08:00',
                'endTime' => '17:00',
                'startBreakTime' => '12:00',
                'endBreakTime' => '13:00',
                'workingDays' => 'Mon,Tue,Wed,Thu,Fri,Sat',
                'totalWorkHours' => 8.00,
                'isDefault' => false,
            ],
            [
                'name' => 'Morning Shift',
                'startTime' => '06:00',
                'endTime' => '14:00',
                'startBreakTime' => '10:00',
                'endBreakTime' => '10:30',
                'workingDays' => 'Mon,Tue,Wed,Thu,Fri,Sat',
                'totalWorkHours' => 7.50,
                'isDefault' => false,
            ],
            [
                'name' => 'Afternoon Shift',
                'startTime' => '14:00',
                'endTime' => '22:00',
                'startBreakTime' => '18:00',
                'endBreakTime' => '18:30',
                'workingDays' => 'Mon,Tue,Wed,Thu,Fri,Sat',
                'totalWorkHours' => 7.50,
                'isDefault' => false,
            ],
            [
                'name' => 'Graveyard Shift',
                'startTime' => '22:00',
                'endTime' => '06:00', // Next day
                'startBreakTime' => '02:00',
                'endBreakTime' => '02:30',
                'workingDays' => 'Mon,Tue,Wed,Thu,Fri,Sat',
                'totalWorkHours' => 7.50,
                'isDefault' => false,
            ],
            [
                'name' => 'Security 12-Hour Day',
                'startTime' => '06:00',
                'endTime' => '18:00',
                'startBreakTime' => '12:00',
                'endBreakTime' => '13:00',
                'workingDays' => 'Mon,Wed,Fri,Sun',
                'totalWorkHours' => 11.00,
                'isDefault' => false,
            ],
            [
                'name' => 'Security 12-Hour Night',
                'startTime' => '18:00',
                'endTime' => '06:00', // Next day
                'startBreakTime' => '00:00',
                'endBreakTime' => '01:00',
                'workingDays' => 'Tue,Thu,Sat',
                'totalWorkHours' => 11.00,
                'isDefault' => false,
            ],
        ];

        foreach ($schedules as $schedule) {
            WorkSchedule::create($schedule);
        }
    }
}
