<?php

namespace App\Support;

use Illuminate\Database\Schema\Blueprint;

class MigrationHelper
{
    /**
     * Add system audit fields to a table.
     * Call this in your migration: MigrationHelper::addSystemFields($table);
     */
    public static function addSystemFields(Blueprint $table): void
    {
        $table->dateTime('CreatedDateTime')->nullable();
        $table->foreignId('CreatedByID')->nullable()->constrained('users')->nullOnDelete();
        $table->dateTime('LastModifiedDateTime')->nullable();
        $table->foreignId('LastModifiedByID')->nullable()->constrained('users')->nullOnDelete();
    }
}
