<?php

namespace Database\Seeders;

use App\Enums\Department;
use App\Enums\EmploymentStatus;
use App\Enums\EmploymentType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Position;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeSeeder extends Seeder
{
    /**
     * Realistic Filipino names for seeding.
     */
    private array $maleFirstNames = [
        'Juan', 'Jose', 'Pedro', 'Carlos', 'Miguel', 'Antonio', 'Rafael', 'Francisco',
        'Manuel', 'Ricardo', 'Fernando', 'Eduardo', 'Roberto', 'Alejandro', 'Enrique',
        'Gabriel', 'Daniel', 'Andres', 'Marco', 'Luis', 'Ramon', 'Ernesto', 'Arturo',
        'Ronaldo', 'Jayson', 'Mark', 'John',  'Michael', 'Vincent', 'Christian',
    ];

    private array $femaleFirstNames = [
        'Maria', 'Ana', 'Rosa', 'Carmen', 'Teresa', 'Lucia', 'Patricia', 'Elena',
        'Isabella', 'Sofia', 'Angelica', 'Gabriela', 'Cristina', 'Veronica', 'Diana',
        'Michelle', 'Jennifer', 'Katherine', 'Stephanie', 'Nicole', 'Angela', 'Grace',
        'Joy', 'Faith', 'Hope', 'Cherry', 'Apple', 'Princess', 'Precious', 'Lovely',
    ];

    private array $lastNames = [
        'Santos', 'Reyes', 'Cruz', 'Bautista', 'Garcia', 'Mendoza', 'Torres', 'Flores',
        'Rivera', 'Gonzales', 'Ramos', 'Castro', 'Dela Cruz', 'Villanueva', 'Fernandez',
        'Lopez', 'Martinez', 'Rodriguez', 'Hernandez', 'Perez', 'Sanchez', 'Ramirez',
        'Aquino', 'Tan', 'Lim', 'Chua', 'Ong', 'Go', 'Sy', 'Co', 'Ang', 'Yap', 'Lee',
        'Pascual', 'Soriano', 'De Leon', 'Valdez', 'Aguilar', 'Salazar', 'Domingo',
    ];

    private array $middleNames = [
        'Delos Santos', 'Dela Cruz', 'De Guzman', 'De Leon', 'Del Rosario',
        'Reyes', 'Santos', 'Garcia', 'Lopez', 'Martinez', 'Bautista', 'Mendoza',
    ];

    /**
     * Seed the employees table.
     */
    public function run(): void
    {
        $schedules = WorkSchedule::all();
        
        if ($schedules->isEmpty()) {
            $this->command->warn('No work schedules found. Please run WorkScheduleSeeder first.');
            return;
        }

        // Get schedule IDs for assignment
        $dayShiftRegular = $schedules->firstWhere('name', 'Day Shift (Regular)');
        $dayShift6Day = $schedules->firstWhere('name', 'Day Shift (6-Day)');
        $morningShift = $schedules->firstWhere('name', 'Morning Shift');
        $afternoonShift = $schedules->firstWhere('name', 'Afternoon Shift');
        $graveyardShift = $schedules->firstWhere('name', 'Graveyard Shift');
        $security12Day = $schedules->firstWhere('name', 'Security 12-Hour Day');
        $security12Night = $schedules->firstWhere('name', 'Security 12-Hour Night');

        // Define employees with realistic role-to-schedule mapping
        $employees = [
            // Management (Day Shift Regular)
            ['position' => Position::GENERAL_MANAGER, 'department' => Department::ADMINISTRATION, 'schedule' => $dayShiftRegular, 'salary' => [80000, 120000]],
            ['position' => Position::OPERATIONS_MANAGER, 'department' => Department::OPERATIONS, 'schedule' => $dayShiftRegular, 'salary' => [60000, 80000]],
            ['position' => Position::HR_MANAGER, 'department' => Department::HUMAN_RESOURCES, 'schedule' => $dayShiftRegular, 'salary' => [50000, 70000]],
            ['position' => Position::FINANCE_MANAGER, 'department' => Department::FINANCE, 'schedule' => $dayShiftRegular, 'salary' => [55000, 75000]],
            ['position' => Position::IT_MANAGER, 'department' => Department::IT, 'schedule' => $dayShiftRegular, 'salary' => [55000, 75000]],

            // Supervisors (Various shifts)
            ['position' => Position::SUPERVISOR, 'department' => Department::OPERATIONS, 'schedule' => $morningShift, 'salary' => [30000, 40000]],
            ['position' => Position::SUPERVISOR, 'department' => Department::OPERATIONS, 'schedule' => $afternoonShift, 'salary' => [30000, 40000]],
            ['position' => Position::SUPERVISOR, 'department' => Department::OPERATIONS, 'schedule' => $graveyardShift, 'salary' => [32000, 42000]],
            ['position' => Position::TEAM_LEAD, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Day, 'salary' => [28000, 35000]],
            ['position' => Position::TEAM_LEAD, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Night, 'salary' => [30000, 38000]],

            // Admin/HR Staff (Day Shift)
            ['position' => Position::ADMIN_CLERK, 'department' => Department::ADMINISTRATION, 'schedule' => $dayShiftRegular, 'salary' => [18000, 25000]],
            ['position' => Position::ADMIN_CLERK, 'department' => Department::ADMINISTRATION, 'schedule' => $dayShiftRegular, 'salary' => [18000, 25000]],
            ['position' => Position::RECRUITMENT_OFFICER, 'department' => Department::HUMAN_RESOURCES, 'schedule' => $dayShiftRegular, 'salary' => [22000, 30000]],

            // IT Staff
            ['position' => Position::IT_SUPPORT, 'department' => Department::IT, 'schedule' => $dayShiftRegular, 'salary' => [25000, 35000]],
            ['position' => Position::IT_SUPPORT, 'department' => Department::IT, 'schedule' => $dayShift6Day, 'salary' => [25000, 35000]],

            // Training
            ['position' => Position::TRAINER, 'department' => Department::TRAINING, 'schedule' => $dayShiftRegular, 'salary' => [25000, 35000]],
            ['position' => Position::TRAINER, 'department' => Department::TRAINING, 'schedule' => $morningShift, 'salary' => [25000, 35000]],

            // Logistics
            ['position' => Position::LOGISTICS_OFFICER, 'department' => Department::LOGISTICS, 'schedule' => $morningShift, 'salary' => [20000, 28000]],
            ['position' => Position::LOGISTICS_OFFICER, 'department' => Department::LOGISTICS, 'schedule' => $afternoonShift, 'salary' => [20000, 28000]],

            // Security Officers - Day shifts
            ['position' => Position::SENIOR_SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Day, 'salary' => [22000, 28000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Day, 'salary' => [18000, 22000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Day, 'salary' => [18000, 22000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $morningShift, 'salary' => [18000, 22000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $morningShift, 'salary' => [18000, 22000]],
            ['position' => Position::ARMED_SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $morningShift, 'salary' => [22000, 28000]],

            // Security Officers - Night/Graveyard shifts
            ['position' => Position::SENIOR_SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Night, 'salary' => [24000, 30000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Night, 'salary' => [20000, 24000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $security12Night, 'salary' => [20000, 24000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $graveyardShift, 'salary' => [20000, 24000]],
            ['position' => Position::SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $graveyardShift, 'salary' => [20000, 24000]],
            ['position' => Position::ARMED_SECURITY_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $graveyardShift, 'salary' => [24000, 30000]],

            // Security Patrol & Control Room
            ['position' => Position::SECURITY_PATROL, 'department' => Department::SECURITY_CONTROL, 'schedule' => $morningShift, 'salary' => [18000, 22000]],
            ['position' => Position::SECURITY_PATROL, 'department' => Department::SECURITY_CONTROL, 'schedule' => $afternoonShift, 'salary' => [18000, 22000]],
            ['position' => Position::SECURITY_PATROL, 'department' => Department::SECURITY_CONTROL, 'schedule' => $graveyardShift, 'salary' => [20000, 24000]],
            ['position' => Position::CONTROL_ROOM_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $morningShift, 'salary' => [20000, 26000]],
            ['position' => Position::CONTROL_ROOM_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $afternoonShift, 'salary' => [20000, 26000]],
            ['position' => Position::CONTROL_ROOM_OFFICER, 'department' => Department::SECURITY_CONTROL, 'schedule' => $graveyardShift, 'salary' => [22000, 28000]],
        ];

        foreach ($employees as $index => $employeeData) {
            $this->createEmployee($employeeData, $index);
        }

        $this->command->info('Created ' . count($employees) . ' employees successfully.');
    }

    /**
     * Create a single employee with associated user.
     */
    private function createEmployee(array $data, int $index): void
    {
        // Determine gender (slightly more male for security agency context)
        $isMale = fake()->boolean(65);
        $gender = $isMale ? Gender::MALE : Gender::FEMALE;

        // Pick appropriate name based on gender
        $firstName = $isMale 
            ? fake()->randomElement($this->maleFirstNames)
            : fake()->randomElement($this->femaleFirstNames);
        $middleName = fake()->optional(0.7)->randomElement($this->middleNames);
        $lastName = fake()->randomElement($this->lastNames);

        // Generate unique email
        $emailBase = strtolower($firstName . '.' . str_replace(' ', '', $lastName));
        $email = $emailBase . ($index > 0 ? $index : '') . '@fortitech.com';

        DB::transaction(function () use ($firstName, $middleName, $lastName, $email, $gender, $data) {
            // Create user
            $user = User::create([
                'firstName' => $firstName,
                'middleName' => $middleName,
                'lastName' => $lastName,
                'email' => $email,
                'email_verified_at' => now(),
                'password' => Hash::make('password123'),
            ]);

            // Calculate hire date (between 5 years ago and 3 months ago)
            $hireDate = fake()->dateTimeBetween('-5 years', '-3 months');

            // Determine employment status and type based on hire date
            $monthsEmployed = now()->diffInMonths($hireDate);
            
            if ($monthsEmployed < 6) {
                $employmentStatus = EmploymentStatus::ACTIVE;
                $employmentType = EmploymentType::PROBATIONARY;
            } else {
                $employmentStatus = fake()->randomElement([
                    EmploymentStatus::ACTIVE,
                    EmploymentStatus::ACTIVE,
                    EmploymentStatus::ACTIVE,
                    EmploymentStatus::ACTIVE,
                    EmploymentStatus::INACTIVE, // 20% chance inactive
                ]);
                $employmentType = fake()->randomElement([
                    EmploymentType::REGULAR,
                    EmploymentType::REGULAR,
                    EmploymentType::REGULAR,
                    EmploymentType::CONTRACTUAL,
                ]);
            }

            // Calculate age-appropriate date of birth (21-55 years old)
            $age = fake()->numberBetween(21, 55);
            $dateOfBirth = now()->subYears($age)->subDays(fake()->numberBetween(0, 364));

            // Generate salary within range
            $salary = fake()->randomFloat(2, $data['salary'][0], $data['salary'][1]);

            // Create employee
            Employee::create([
                'userID' => $user->id,
                'workScheduleID' => $data['schedule']->id,
                'dateOfBirth' => $dateOfBirth,
                'gender' => $gender,
                'maritalStatus' => fake()->randomElement(MaritalStatus::cases()),
                'address' => fake()->streetAddress() . ', ' . fake()->randomElement([
                    'Makati City', 'Quezon City', 'Manila', 'Pasig City', 'Taguig City',
                    'Mandaluyong City', 'San Juan City', 'Parañaque City', 'Las Piñas City',
                    'Muntinlupa City', 'Caloocan City', 'Marikina City', 'Pasay City',
                ]) . ', Metro Manila',
                'phoneNbr' => '09' . fake()->numerify('#########'),
                'hireDate' => $hireDate,
                'employmentStatus' => $employmentStatus,
                'employmentType' => $employmentType,
                'department' => $data['department'],
                'jobTitle' => $data['position'],
                'basicMonthlySalary' => $salary,
                'emergencyContactName' => fake()->name(),
                'emergencyContactPhoneNbr' => '09' . fake()->numerify('#########'),
            ]);
        });
    }
}
