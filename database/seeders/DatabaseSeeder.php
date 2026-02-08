<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'firstName' => 'Admin',
            'middleName' => '',
            'lastName' => 'User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123!'),
        ]);

        // Run seeders in order (respecting foreign key dependencies)
        $this->call([
            WorkScheduleSeeder::class,  // Must be first (employees reference work schedules)
            EmployeeSeeder::class,       // Creates users and employees
            AttendanceRecordSeeder::class, // Needs employees with schedules
        ]);
    }
}
