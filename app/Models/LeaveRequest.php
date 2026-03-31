<?php

namespace App\Models;

use App\Enums\HalfDayPeriod;
use App\Enums\LeaveStatus;
use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveRequestFactory> */
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'employeeID',
        'leaveTypeID',
        'startDate',
        'endDate',
        'totalDays',
        'isHalfDay',
        'halfDayPeriod',
        'reason',
        'status',
        'approvedByID',
        'approvedAt',
        'rejectionReason',
    ];

    protected function casts(): array
    {
        return [
            'startDate' => 'date',
            'endDate' => 'date',
            'totalDays' => 'decimal:2',
            'isHalfDay' => 'boolean',
            'halfDayPeriod' => HalfDayPeriod::class,
            'status' => LeaveStatus::class,
            'approvedAt' => 'datetime',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeID', 'employeeID');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leaveTypeID');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approvedByID');
    }
}
