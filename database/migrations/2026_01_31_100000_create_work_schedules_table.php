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
        Schema::create('work_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->time('startTime');
            $table->time('endTime');
            $table->time('startBreakTime')->nullable();
            $table->time('endBreakTime')->nullable();
            $table->string('workingDays', 100); // e.g., "Mon,Tue,Wed,Thu,Fri"
            $table->decimal('totalWorkHours', 5, 2)->default(0); // Total work hours (work time - break time)
            $table->boolean('isDefault')->default(false);
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_schedules');
    }
};
