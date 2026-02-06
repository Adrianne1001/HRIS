<?php

namespace App\Models;

use App\Traits\HasSystemFields;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    /** @use HasFactory<\Database\Factories\AttendanceRecordFactory> */
    use HasFactory, HasSystemFields;

    /**
     * Remarks enum values.
     */
    public const REMARKS_LATE = 'Late';
    public const REMARKS_UNDERTIME = 'Undertime';
    public const REMARKS_NO_TIME_IN = 'No Time In';
    public const REMARKS_NO_TIME_OUT = 'No Time Out';
    public const REMARKS_VACATION_LEAVE = 'Vacation Leave';
    public const REMARKS_SICK_LEAVE = 'Sick Leave';

    /**
     * In or Out enum values.
     */
    public const IN = 'In';
    public const OUT = 'Out';

    /**
     * Get all available remarks options.
     */
    public static function remarksOptions(): array
    {
        return [
            self::REMARKS_LATE,
            self::REMARKS_UNDERTIME,
            self::REMARKS_NO_TIME_IN,
            self::REMARKS_NO_TIME_OUT,
            self::REMARKS_VACATION_LEAVE,
            self::REMARKS_SICK_LEAVE,
        ];
    }

    /**
     * Get all available inOrOut options.
     */
    public static function inOrOutOptions(): array
    {
        return [
            self::IN,
            self::OUT,
        ];
    }

    protected $fillable = [
        'employee_id',
        'workDate',
        'image',
        'inOrOut',
        'shiftTimeIn',
        'shiftTimeOut',
        'actualTimeIn',
        'actualTimeOut',
        'hoursWorked',
        'advanceOTHours',
        'afterShiftOTHours',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'workDate' => 'date',
            'shiftTimeIn' => 'datetime',
            'shiftTimeOut' => 'datetime',
            'actualTimeIn' => 'datetime',
            'actualTimeOut' => 'datetime',
            'hoursWorked' => 'decimal:2',
            'advanceOTHours' => 'decimal:2',
            'afterShiftOTHours' => 'decimal:2',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    /**
     * Get the employee that owns this attendance record.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * Calculate hours worked based on actual times, capped at employee's work schedule totalWorkHours.
     */
    public function calculateHoursWorked(): float
    {
        if (!$this->actualTimeIn || !$this->actualTimeOut) {
            return 0;
        }

        // All time fields are now full datetime values
        $timeIn = Carbon::parse($this->actualTimeIn);
        $timeOut = Carbon::parse($this->actualTimeOut);
        $shiftIn = Carbon::parse($this->shiftTimeIn);
        $shiftOut = Carbon::parse($this->shiftTimeOut);

        // Use the later of actualTimeIn or shiftTimeIn as effective start
        $effectiveStart = $timeIn->greaterThan($shiftIn) ? $timeIn : $shiftIn;

        // Use the earlier of actualTimeOut or shiftTimeOut as effective end
        $effectiveEnd = $timeOut->lessThan($shiftOut) ? $timeOut : $shiftOut;

        // Handle edge case where effective end is before effective start
        if ($effectiveEnd->lessThanOrEqualTo($effectiveStart)) {
            return 0;
        }

        $workedMinutes = $effectiveStart->diffInMinutes($effectiveEnd);
        $hoursWorked = round($workedMinutes / 60, 2);

        // Cap at employee's work schedule totalWorkHours
        $maxHours = $this->getMaxWorkHours();
        
        return min($hoursWorked, $maxHours);
    }

    /**
     * Calculate advance OT hours (when employee arrives before shift starts).
     */
    public function calculateAdvanceOTHours(): float
    {
        if (!$this->actualTimeIn || !$this->shiftTimeIn) {
            return 0;
        }

        $timeIn = Carbon::parse($this->actualTimeIn);
        $shiftIn = Carbon::parse($this->shiftTimeIn);

        // If employee arrived before shift start time
        if ($timeIn->lessThan($shiftIn)) {
            $minutes = $timeIn->diffInMinutes($shiftIn);
            return round($minutes / 60, 2);
        }

        return 0;
    }

    /**
     * Calculate after-shift OT hours (when employee leaves after shift ends).
     */
    public function calculateAfterShiftOTHours(): float
    {
        if (!$this->actualTimeOut || !$this->shiftTimeOut) {
            return 0;
        }

        $timeOut = Carbon::parse($this->actualTimeOut);
        $shiftOut = Carbon::parse($this->shiftTimeOut);

        // If employee left after shift end time
        if ($timeOut->greaterThan($shiftOut)) {
            $minutes = $shiftOut->diffInMinutes($timeOut);
            return round($minutes / 60, 2);
        }

        return 0;
    }

    /**
     * Get the maximum work hours from employee's work schedule.
     */
    public function getMaxWorkHours(): float
    {
        $employee = $this->employee;
        
        if ($employee && $employee->workSchedule) {
            return (float) $employee->workSchedule->totalWorkHours;
        }

        // Default to 8 hours if no work schedule
        return 8.0;
    }

    /**
     * Calculate and set all computed fields.
     */
    public function calculateAllFields(): void
    {
        $this->hoursWorked = $this->calculateHoursWorked();
        $this->advanceOTHours = $this->calculateAdvanceOTHours();
        $this->afterShiftOTHours = $this->calculateAfterShiftOTHours();
    }

    /**
     * Determine remarks based on actual times.
     */
    public function determineRemarks(): ?string
    {
        if (!$this->actualTimeIn && !$this->actualTimeOut) {
            return null; // Could be leave, set manually
        }

        if (!$this->actualTimeIn) {
            return self::REMARKS_NO_TIME_IN;
        }

        if (!$this->actualTimeOut) {
            return self::REMARKS_NO_TIME_OUT;
        }

        $timeIn = Carbon::parse($this->actualTimeIn);
        $shiftIn = Carbon::parse($this->shiftTimeIn);

        if ($timeIn->greaterThan($shiftIn)) {
            return self::REMARKS_LATE;
        }

        return null;
    }

    /**
     * Get formatted hours worked display.
     */
    public function getFormattedHoursWorkedAttribute(): string
    {
        $hours = floor($this->hoursWorked);
        $minutes = round(($this->hoursWorked - $hours) * 60);

        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }

    /**
     * Get formatted advance OT hours display.
     */
    public function getFormattedAdvanceOTHoursAttribute(): string
    {
        if ($this->advanceOTHours <= 0) {
            return '-';
        }

        $hours = floor($this->advanceOTHours);
        $minutes = round(($this->advanceOTHours - $hours) * 60);

        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }

    /**
     * Get formatted after-shift OT hours display.
     */
    public function getFormattedAfterShiftOTHoursAttribute(): string
    {
        if ($this->afterShiftOTHours <= 0) {
            return '-';
        }

        $hours = floor($this->afterShiftOTHours);
        $minutes = round(($this->afterShiftOTHours - $hours) * 60);

        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }

    /**
     * Check if employee was late.
     */
    public function isLate(): bool
    {
        return $this->remarks === self::REMARKS_LATE;
    }

    /**
     * Check if this is a leave record.
     */
    public function isLeave(): bool
    {
        return in_array($this->remarks, [
            self::REMARKS_VACATION_LEAVE,
            self::REMARKS_SICK_LEAVE,
        ]);
    }

    /**
     * Get total OT hours (advance + after-shift).
     */
    public function getTotalOTHoursAttribute(): float
    {
        return $this->advanceOTHours + $this->afterShiftOTHours;
    }

    /**
     * Get formatted total OT hours display.
     */
    public function getFormattedTotalOTHoursAttribute(): string
    {
        $total = $this->totalOTHours;
        
        if ($total <= 0) {
            return '-';
        }

        $hours = floor($total);
        $minutes = round(($total - $hours) * 60);

        return $hours . 'h' . ($minutes > 0 ? ' ' . $minutes . 'm' : '');
    }
}
