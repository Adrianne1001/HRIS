<?php
namespace App\Models;

use App\Enums\HolidayType;
use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'name', 'date', 'holidayType', 'year',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'holidayType' => HolidayType::class,
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }
}
