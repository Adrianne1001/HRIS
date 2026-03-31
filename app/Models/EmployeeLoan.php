<?php
namespace App\Models;

use App\Enums\LoanType;
use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmployeeLoan extends Model
{
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'employeeID', 'loanType', 'referenceNbr', 'loanAmount', 'monthlyAmortization',
        'totalPaid', 'remainingBalance', 'startDate', 'endDate', 'isActive', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'loanType' => LoanType::class,
            'loanAmount' => 'decimal:2',
            'monthlyAmortization' => 'decimal:2',
            'totalPaid' => 'decimal:2',
            'remainingBalance' => 'decimal:2',
            'startDate' => 'date',
            'endDate' => 'date',
            'isActive' => 'boolean',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeID', 'employeeID');
    }

    public function payrollDeductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class, 'employeeLoanID');
    }
}
