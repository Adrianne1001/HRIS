<?php

namespace App\Models;

use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkSchedule extends Model
{
    /** @use HasFactory<\Database\Factories\WorkScheduleFactory> */
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'name',
        'startTime',
        'endTime',
        'startBreakTime',
        'endBreakTime',
        'workingDays',
        'totalWorkHours',
        'isDefault',
    ];

    protected function casts(): array
    {
        return [
            'startTime' => 'datetime:H:i',
            'endTime' => 'datetime:H:i',
            'startBreakTime' => 'datetime:H:i',
            'endBreakTime' => 'datetime:H:i',
            'totalWorkHours' => 'decimal:2',
            'isDefault' => 'boolean',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    /**
     * Get formatted total work hours (e.g., "8h" or "8h 30m").
     */
    public function getFormattedTotalHoursAttribute(): string
    {
        $hours = floor($this->totalWorkHours);
        $minutes = round(($this->totalWorkHours - $hours) * 60);
        
        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }

    /**
     * Calculate total work hours from time fields.
     */
    public static function calculateTotalWorkHours(string $startTime, string $endTime, ?string $startBreakTime = null, ?string $endBreakTime = null): float
    {
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($endTime);
        
        // If end time is before start time, it means the shift crosses midnight (overnight shift)
        if ($end->lessThanOrEqualTo($start)) {
            $end->addDay();
        }
        
        $totalMinutes = $start->diffInMinutes($end);
        
        if ($startBreakTime && $endBreakTime) {
            $breakStart = \Carbon\Carbon::parse($startBreakTime);
            $breakEnd = \Carbon\Carbon::parse($endBreakTime);
            
            // Handle overnight break times too
            if ($breakEnd->lessThanOrEqualTo($breakStart)) {
                $breakEnd->addDay();
            }
            
            $breakMinutes = $breakStart->diffInMinutes($breakEnd);
            $totalMinutes -= $breakMinutes;
        }
        
        return round(max(0, $totalMinutes) / 60, 2);
    }

    /**
     * Get the default work schedule.
     */
    public static function getDefault(): ?self
    {
        return static::where('isDefault', true)->first();
    }

    /**
     * Set this work schedule as the default.
     */
    public function setAsDefault(): void
    {
        // Remove default from all other schedules
        static::where('isDefault', true)->update(['isDefault' => false]);
        
        // Set this one as default
        $this->update(['isDefault' => true]);
    }

    /**
     * Get the working days as an array.
     */
    public function getWorkingDaysArrayAttribute(): array
    {
        return $this->workingDays ? explode(',', $this->workingDays) : [];
    }

    /**
     * Set the working days from an array.
     */
    public function setWorkingDaysFromArray(array $days): void
    {
        $this->workingDays = implode(',', $days);
    }

    /**
     * Get the employees assigned to this work schedule.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class, 'workScheduleID');
    }
}
