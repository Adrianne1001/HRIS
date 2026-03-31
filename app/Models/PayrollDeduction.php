<?php
namespace App\Models;

use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayrollDeduction extends Model
{
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'payrollRecordID', 'deductionTypeID', 'employeeLoanID',
        'description', 'employeeAmount', 'employerAmount', 'remarks',
    ];

    protected function casts(): array
    {
        return [
            'employeeAmount' => 'decimal:2',
            'employerAmount' => 'decimal:2',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function payrollRecord(): BelongsTo
    {
        return $this->belongsTo(PayrollRecord::class, 'payrollRecordID');
    }

    public function deductionType(): BelongsTo
    {
        return $this->belongsTo(DeductionType::class, 'deductionTypeID');
    }

    public function employeeLoan(): BelongsTo
    {
        return $this->belongsTo(EmployeeLoan::class, 'employeeLoanID');
    }
}
