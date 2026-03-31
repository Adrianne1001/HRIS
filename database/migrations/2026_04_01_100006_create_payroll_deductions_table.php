<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payrollRecordID')->constrained('payroll_records')->cascadeOnDelete();
            $table->foreignId('deductionTypeID')->nullable()->constrained('deduction_types');
            $table->foreignId('employeeLoanID')->nullable()->constrained('employee_loans');
            $table->string('description');
            $table->decimal('employeeAmount', 10, 2);
            $table->decimal('employerAmount', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_deductions');
    }
};
