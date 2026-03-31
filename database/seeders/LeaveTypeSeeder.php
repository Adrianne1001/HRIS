<?php

namespace Database\Seeders;

use App\Enums\Gender;
use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $leaveTypes = [
            [
                'name' => 'Vacation Leave',
                'code' => 'VL',
                'defaultCredits' => 15.00,
                'description' => 'Annual vacation leave entitlement',
                'isActive' => true,
                'isPaid' => true,
                'requiresDocument' => false,
                'maxConsecutiveDays' => null,
                'gender' => null,
            ],
            [
                'name' => 'Sick Leave',
                'code' => 'SL',
                'defaultCredits' => 15.00,
                'description' => 'Annual sick leave entitlement',
                'isActive' => true,
                'isPaid' => true,
                'requiresDocument' => true,
                'maxConsecutiveDays' => null,
                'gender' => null,
            ],
            [
                'name' => 'Emergency Leave',
                'code' => 'EL',
                'defaultCredits' => 3.00,
                'description' => 'Emergency leave for urgent personal matters',
                'isActive' => true,
                'isPaid' => true,
                'requiresDocument' => false,
                'maxConsecutiveDays' => null,
                'gender' => null,
            ],
            [
                'name' => 'Maternity Leave',
                'code' => 'ML',
                'defaultCredits' => 105.00,
                'description' => 'Maternity leave per Republic Act No. 11210',
                'isActive' => true,
                'isPaid' => true,
                'requiresDocument' => true,
                'maxConsecutiveDays' => null,
                'gender' => Gender::FEMALE,
            ],
            [
                'name' => 'Paternity Leave',
                'code' => 'PL',
                'defaultCredits' => 7.00,
                'description' => 'Paternity leave per Republic Act No. 8187',
                'isActive' => true,
                'isPaid' => true,
                'requiresDocument' => true,
                'maxConsecutiveDays' => null,
                'gender' => Gender::MALE,
            ],
            [
                'name' => 'Bereavement Leave',
                'code' => 'BL',
                'defaultCredits' => 3.00,
                'description' => 'Leave for death of immediate family member',
                'isActive' => true,
                'isPaid' => true,
                'requiresDocument' => true,
                'maxConsecutiveDays' => null,
                'gender' => null,
            ],
            [
                'name' => 'Solo Parent Leave',
                'code' => 'SPL',
                'defaultCredits' => 7.00,
                'description' => 'Solo parent leave per Republic Act No. 8972',
                'isActive' => true,
                'isPaid' => true,
                'requiresDocument' => true,
                'maxConsecutiveDays' => null,
                'gender' => null,
            ],
        ];

        foreach ($leaveTypes as $leaveType) {
            LeaveType::create($leaveType);
        }
    }
}
