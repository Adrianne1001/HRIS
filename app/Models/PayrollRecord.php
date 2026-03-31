<?php
namespace App\Models;

use App\Traits\HasSystemFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PayrollRecord extends Model
{
    use HasFactory, HasSystemFields;

    protected $fillable = [
        'payrollPeriodID', 'employeeID', 'basicMonthlySalary', 'dailyRate', 'hourlyRate',
        'daysWorked', 'daysAbsent', 'approvedLeaveDays', 'regularHoursWorked', 'overtimeHours',
        'nightDifferentialHours', 'holidayDaysWorked', 'specialHolidayDaysWorked',
        'lateMinutes', 'undertimeMinutes',
        'basicPay', 'absentDeduction', 'lateDeduction', 'undertimeDeduction',
        'overtimePay', 'nightDifferentialPay', 'holidayPay', 'specialHolidayPay', 'grossPay',
        'sssEmployee', 'sssEmployer', 'sssEC', 'philhealthEmployee', 'philhealthEmployer',
        'pagibigEmployee', 'pagibigEmployer', 'withholdingTax',
        'totalMandatoryDeductions', 'totalLoanDeductions', 'totalDeductions', 'netPay',
    ];

    protected function casts(): array
    {
        return [
            'basicMonthlySalary' => 'decimal:2',
            'dailyRate' => 'decimal:2',
            'hourlyRate' => 'decimal:2',
            'daysWorked' => 'decimal:2',
            'daysAbsent' => 'decimal:2',
            'approvedLeaveDays' => 'decimal:2',
            'regularHoursWorked' => 'decimal:2',
            'overtimeHours' => 'decimal:2',
            'nightDifferentialHours' => 'decimal:2',
            'holidayDaysWorked' => 'decimal:2',
            'specialHolidayDaysWorked' => 'decimal:2',
            'lateMinutes' => 'decimal:2',
            'undertimeMinutes' => 'decimal:2',
            'basicPay' => 'decimal:2',
            'absentDeduction' => 'decimal:2',
            'lateDeduction' => 'decimal:2',
            'undertimeDeduction' => 'decimal:2',
            'overtimePay' => 'decimal:2',
            'nightDifferentialPay' => 'decimal:2',
            'holidayPay' => 'decimal:2',
            'specialHolidayPay' => 'decimal:2',
            'grossPay' => 'decimal:2',
            'sssEmployee' => 'decimal:2',
            'sssEmployer' => 'decimal:2',
            'sssEC' => 'decimal:2',
            'philhealthEmployee' => 'decimal:2',
            'philhealthEmployer' => 'decimal:2',
            'pagibigEmployee' => 'decimal:2',
            'pagibigEmployer' => 'decimal:2',
            'withholdingTax' => 'decimal:2',
            'totalMandatoryDeductions' => 'decimal:2',
            'totalLoanDeductions' => 'decimal:2',
            'totalDeductions' => 'decimal:2',
            'netPay' => 'decimal:2',
            'CreatedDateTime' => 'datetime',
            'LastModifiedDateTime' => 'datetime',
        ];
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class, 'payrollPeriodID');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employeeID', 'employeeID');
    }

    public function payrollDeductions(): HasMany
    {
        return $this->hasMany(PayrollDeduction::class, 'payrollRecordID');
    }
}
