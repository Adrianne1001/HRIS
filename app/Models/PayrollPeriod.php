<?php
namespace App\Models;

use App\Enums\PayrollPeriodStatus;
use App\Enums\PayrollPeriodType;
use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollPeriod extends Model
{
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'name', 'periodType', 'startDate', 'endDate', 'payDate', 'status',
        'totalGrossPay', 'totalDeductions', 'totalNetPay', 'totalEmployerContributions',
        'employeeCount', 'processedAt', 'completedAt',
    ];

    protected function casts(): array
    {
        return [
            'startDate' => 'date',
            'endDate' => 'date',
            'payDate' => 'date',
            'periodType' => PayrollPeriodType::class,
            'status' => PayrollPeriodStatus::class,
            'totalGrossPay' => 'decimal:2',
            'totalDeductions' => 'decimal:2',
            'totalNetPay' => 'decimal:2',
            'totalEmployerContributions' => 'decimal:2',
            'processedAt' => 'datetime',
            'completedAt' => 'datetime',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function payrollRecords(): HasMany
    {
        return $this->hasMany(PayrollRecord::class, 'payrollPeriodID');
    }
}
