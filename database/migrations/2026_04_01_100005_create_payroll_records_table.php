<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payrollPeriodID')->constrained('payroll_periods')->cascadeOnDelete();
            $table->unsignedBigInteger('employeeID');
            $table->foreign('employeeID')->references('employeeID')->on('employees')->cascadeOnDelete();
            // Salary/rate
            $table->decimal('basicMonthlySalary', 12, 2);
            $table->decimal('dailyRate', 10, 2);
            $table->decimal('hourlyRate', 10, 2);
            // Attendance
            $table->decimal('daysWorked', 5, 2);
            $table->decimal('daysAbsent', 5, 2);
            $table->decimal('approvedLeaveDays', 5, 2)->default(0);
            $table->decimal('regularHoursWorked', 7, 2);
            $table->decimal('overtimeHours', 7, 2);
            $table->decimal('nightDifferentialHours', 7, 2)->default(0);
            $table->decimal('holidayDaysWorked', 5, 2)->default(0);
            $table->decimal('specialHolidayDaysWorked', 5, 2)->default(0);
            $table->decimal('lateMinutes', 7, 2)->default(0);
            $table->decimal('undertimeMinutes', 7, 2)->default(0);
            // Earnings
            $table->decimal('basicPay', 12, 2);
            $table->decimal('absentDeduction', 12, 2)->default(0);
            $table->decimal('lateDeduction', 12, 2)->default(0);
            $table->decimal('undertimeDeduction', 12, 2)->default(0);
            $table->decimal('overtimePay', 12, 2)->default(0);
            $table->decimal('nightDifferentialPay', 12, 2)->default(0);
            $table->decimal('holidayPay', 12, 2)->default(0);
            $table->decimal('specialHolidayPay', 12, 2)->default(0);
            $table->decimal('grossPay', 12, 2);
            // Mandatory deductions
            $table->decimal('sssEmployee', 10, 2)->default(0);
            $table->decimal('sssEmployer', 10, 2)->default(0);
            $table->decimal('sssEC', 10, 2)->default(0);
            $table->decimal('philhealthEmployee', 10, 2)->default(0);
            $table->decimal('philhealthEmployer', 10, 2)->default(0);
            $table->decimal('pagibigEmployee', 10, 2)->default(0);
            $table->decimal('pagibigEmployer', 10, 2)->default(0);
            $table->decimal('withholdingTax', 10, 2)->default(0);
            // Totals
            $table->decimal('totalMandatoryDeductions', 12, 2)->default(0);
            $table->decimal('totalLoanDeductions', 12, 2)->default(0);
            $table->decimal('totalDeductions', 12, 2)->default(0);
            $table->decimal('netPay', 12, 2);
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
            
            $table->unique(['payrollPeriodID', 'employeeID']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_records');
    }
};
