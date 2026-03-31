<?php

namespace App\Models;

use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBalance extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveBalanceFactory> */
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'employeeID',
        'leaveTypeID',
        'year',
        'totalCredits',
        'usedCredits',
        'pendingCredits',
    ];

    protected function casts(): array
    {
        return [
            'totalCredits' => 'decimal:2',
            'usedCredits' => 'decimal:2',
            'pendingCredits' => 'decimal:2',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function getRemainingCreditsAttribute(): float
    {
        return $this->totalCredits - $this->usedCredits - $this->pendingCredits;
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeID', 'employeeID');
    }

    public function leaveType(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class, 'leaveTypeID');
    }
}
