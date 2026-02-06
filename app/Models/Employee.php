<?php

namespace App\Models;

use App\Enums\Department;
use App\Enums\EmploymentStatus;
use App\Enums\EmploymentType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Position;
use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    /** @use HasFactory<\Database\Factories\EmployeeFactory> */
    use HasFactory, HasSystemFields;

    protected $primaryKey = 'employeeID';

    protected $fillable = [
        'userID',
        'workScheduleID',
        'dateOfBirth',
        'gender',
        'maritalStatus',
        'address',
        'phoneNbr',
        'hireDate',
        'employmentStatus',
        'employmentType',
        'department',
        'jobTitle',
        'basicMonthlySalary',
        'emergencyContactName',
        'emergencyContactPhoneNbr',
        'profilePic',
    ];

    protected function casts(): array
    {
        return [
            'dateOfBirth' => 'date',
            'hireDate' => 'date',
            'gender' => Gender::class,
            'maritalStatus' => MaritalStatus::class,
            'employmentStatus' => EmploymentStatus::class,
            'employmentType' => EmploymentType::class,
            'department' => Department::class,
            'jobTitle' => Position::class,
            'basicMonthlySalary' => 'decimal:2',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime'
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userID');
    }

    public function workSchedule(): BelongsTo
    {
        return $this->belongsTo(WorkSchedule::class, 'workScheduleID');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class, 'employee_id', 'employeeID');
    }
}
