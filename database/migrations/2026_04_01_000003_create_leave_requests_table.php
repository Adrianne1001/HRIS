<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employeeID');
            $table->foreign('employeeID')->references('employeeID')->on('employees')->cascadeOnDelete();
            $table->foreignId('leaveTypeID')->constrained('leave_types')->cascadeOnDelete();
            $table->date('startDate');
            $table->date('endDate');
            $table->decimal('totalDays', 5, 2);
            $table->boolean('isHalfDay')->default(false);
            $table->string('halfDayPeriod')->nullable();
            $table->text('reason');
            $table->string('status')->default('Pending');
            $table->foreignId('approvedByID')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('approvedAt')->nullable();
            $table->text('rejectionReason')->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
    }
};
