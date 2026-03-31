<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 50)->unique();
            $table->decimal('defaultCredits', 5, 2);
            $table->text('description')->nullable();
            $table->boolean('isActive')->default(true);
            $table->boolean('isPaid')->default(true);
            $table->boolean('requiresDocument')->default(false);
            $table->integer('maxConsecutiveDays')->nullable();
            $table->string('gender')->nullable();
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_types');
    }
};
