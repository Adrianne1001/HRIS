<?php

use App\Support\MigrationHelper;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191);
            $table->date('date');
            $table->string('holidayType');
            $table->integer('year');
            $table->timestamps();
            MigrationHelper::addSystemFields($table);
            
            $table->unique(['date', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
    }
};
