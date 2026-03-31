<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('periodType');
            $table->date('startDate');
            $table->date('endDate');
            $table->date('payDate');
            $table->string('status')->default('Draft');
            $table->decimal('totalGrossPay', 12, 2)->default(0);
            $table->decimal('totalDeductions', 12, 2)->default(0);
            $table->decimal('totalNetPay', 12, 2)->default(0);
            $table->decimal('totalEmployerContributions', 12, 2)->default(0);
            $table->integer('employeeCount')->default(0);
            $table->dateTime('processedAt')->nullable();
            $table->dateTime('completedAt')->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
            
            $table->unique(['startDate', 'endDate']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payroll_periods');
    }
};
