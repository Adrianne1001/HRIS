<?php
namespace App\Models;

use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeductionType extends Model
{
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'name', 'code', 'description', 'computationMethod', 'isStatutory', 'isActive',
        'fixedAmount', 'employeeRate', 'employerRate', 'bracketTable',
        'salaryFloor', 'salaryCeiling', 'maxEmployeeAmount', 'maxEmployerAmount',
    ];

    protected function casts(): array
    {
        return [
            'isStatutory' => 'boolean',
            'isActive' => 'boolean',
            'fixedAmount' => 'decimal:2',
            'employeeRate' => 'decimal:4',
            'employerRate' => 'decimal:4',
            'bracketTable' => 'array',
            'salaryFloor' => 'decimal:2',
            'salaryCeiling' => 'decimal:2',
            'maxEmployeeAmount' => 'decimal:2',
            'maxEmployerAmount' => 'decimal:2',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function payrollDeductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class, 'deductionTypeID');
    }
}
