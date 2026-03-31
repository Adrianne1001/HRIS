<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employeeID');
            $table->foreign('employeeID')->references('employeeID')->on('employees')->cascadeOnDelete();
            $table->string('loanType');
            $table->string('referenceNbr')->nullable();
            $table->decimal('loanAmount', 12, 2);
            $table->decimal('monthlyAmortization', 10, 2);
            $table->decimal('totalPaid', 12, 2)->default(0);
            $table->decimal('remainingBalance', 12, 2);
            $table->date('startDate');
            $table->date('endDate')->nullable();
            $table->boolean('isActive')->default(true);
            $table->text('remarks')->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
    }
};
