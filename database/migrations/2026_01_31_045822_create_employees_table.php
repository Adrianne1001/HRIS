<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id('employeeID');
            $table->foreignId('userID')->constrained('users')->cascadeOnDelete();
            $table->foreignId('workScheduleID')->nullable()->constrained('work_schedules')->nullOnDelete();
            $table->date('dateOfBirth');
            $table->string('gender');
            $table->string('maritalStatus');
            $table->string('address', 255);
            $table->string('phoneNbr', 15);
            $table->date('hireDate');
            $table->string('employmentStatus');
            $table->string('employmentType');
            $table->string('department');
            $table->string('jobTitle');
            $table->decimal('basicMonthlySalary', 12, 2);
            $table->string('emergencyContactName')->nullable();
            $table->string('emergencyContactPhoneNbr', 15)->nullable();
            $table->string('profilePic', 255)->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
