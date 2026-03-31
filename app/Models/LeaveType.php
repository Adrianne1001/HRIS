<?php

namespace App\Models;

use App\Enums\Gender;
use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaveType extends Model
{
    /** @use HasFactory<\Database\Factories\LeaveTypeFactory> */
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'name',
        'code',
        'defaultCredits',
        'description',
        'isActive',
        'isPaid',
        'requiresDocument',
        'maxConsecutiveDays',
        'gender',
    ];

    protected function casts(): array
    {
        return [
            'defaultCredits' => 'decimal:2',
            'isActive' => 'boolean',
            'isPaid' => 'boolean',
            'requiresDocument' => 'boolean',
            'gender' => Gender::class,
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function leaveBalances(): HasMany
    {
        return $this->hasMany(LeaveBalance::class, 'leaveTypeID');
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'leaveTypeID');
    }
}
