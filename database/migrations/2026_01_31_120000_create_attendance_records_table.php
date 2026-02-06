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
        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->date('workDate');
            $table->string('image')->nullable();
            $table->enum('inOrOut', ['In', 'Out'])->nullable();
            $table->dateTime('shiftTimeIn');
            $table->dateTime('shiftTimeOut');
            $table->dateTime('actualTimeIn')->nullable();
            $table->dateTime('actualTimeOut')->nullable();
            $table->decimal('hoursWorked', 5, 2)->default(0);
            $table->decimal('advanceOTHours', 5, 2)->default(0);
            $table->decimal('afterShiftOTHours', 5, 2)->default(0);
            $table->enum('remarks', [
                'Late',
                'Undertime',
                'No Time In',
                'No Time Out',
                'Vacation Leave',
                'Sick Leave',
            ])->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);

            // Prevent duplicate attendance records for same employee on same date
            $table->unique(['employee_id', 'workDate']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_records');
    }
};
