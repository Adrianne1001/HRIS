<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employeeID');
            $table->foreign('employeeID')->references('employeeID')->on('employees')->cascadeOnDelete();
            $table->foreignId('leaveTypeID')->constrained('leave_types')->cascadeOnDelete();
            $table->integer('year');
            $table->decimal('totalCredits', 5, 2);
            $table->decimal('usedCredits', 5, 2)->default(0);
            $table->decimal('pendingCredits', 5, 2)->default(0);
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
            
            $table->unique(['employeeID', 'leaveTypeID', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
