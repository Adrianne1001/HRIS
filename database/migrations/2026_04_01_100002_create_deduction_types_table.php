<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deduction_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code',191)->unique();
            $table->text('description')->nullable();
            $table->string('computationMethod');
            $table->boolean('isStatutory')->default(false);
            $table->boolean('isActive')->default(true);
            $table->decimal('fixedAmount', 10, 2)->nullable();
            $table->decimal('employeeRate', 5, 4)->nullable();
            $table->decimal('employerRate', 5, 4)->nullable();
            $table->json('bracketTable')->nullable();
            $table->decimal('salaryFloor', 12, 2)->nullable();
            $table->decimal('salaryCeiling', 12, 2)->nullable();
            $table->decimal('maxEmployeeAmount', 10, 2)->nullable();
            $table->decimal('maxEmployerAmount', 10, 2)->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deduction_types');
    }
};
