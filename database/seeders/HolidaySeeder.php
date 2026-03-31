<?php

namespace Database\Seeders;

use App\Enums\HolidayType;
use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $holidays = [
            // Regular Holidays
            ['name' => "New Year's Day", 'date' => '2026-01-01', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Araw ng Kagitingan', 'date' => '2026-04-09', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Maundy Thursday', 'date' => '2026-04-02', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Good Friday', 'date' => '2026-04-03', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Labor Day', 'date' => '2026-05-01', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Independence Day', 'date' => '2026-06-12', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'National Heroes Day', 'date' => '2026-08-31', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Bonifacio Day', 'date' => '2026-11-30', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Christmas Day', 'date' => '2026-12-25', 'holidayType' => HolidayType::REGULAR],
            ['name' => 'Rizal Day', 'date' => '2026-12-30', 'holidayType' => HolidayType::REGULAR],
            // Special Non-Working Days
            ['name' => 'Ninoy Aquino Day', 'date' => '2026-08-21', 'holidayType' => HolidayType::SPECIAL_NON_WORKING],
            ['name' => "All Saints' Day", 'date' => '2026-11-01', 'holidayType' => HolidayType::SPECIAL_NON_WORKING],
            ['name' => 'Feast of the Immaculate Conception', 'date' => '2026-12-08', 'holidayType' => HolidayType::SPECIAL_NON_WORKING],
            ['name' => 'Last Day of the Year', 'date' => '2026-12-31', 'holidayType' => HolidayType::SPECIAL_NON_WORKING],
            ['name' => 'Chinese New Year', 'date' => '2026-02-17', 'holidayType' => HolidayType::SPECIAL_NON_WORKING],
            ['name' => 'EDSA Anniversary', 'date' => '2026-02-25', 'holidayType' => HolidayType::SPECIAL_NON_WORKING],
            ['name' => 'Black Saturday', 'date' => '2026-04-04', 'holidayType' => HolidayType::SPECIAL_NON_WORKING],
        ];

        foreach ($holidays as $holiday) {
            Holiday::create([
                'name' => $holiday['name'],
                'date' => $holiday['date'],
                'holidayType' => $holiday['holidayType'],
                'year' => (int) substr($holiday['date'], 0, 4),
            ]);
        }
    }
}
