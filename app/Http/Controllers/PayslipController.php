<?php

namespace App\Http\Controllers;

use App\Enums\PayrollPeriodStatus;
use App\Models\Employee;
use App\Models\PayrollRecord;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayslipController extends Controller
{
    public function index()
    {
        $employee = Employee::where('userID', Auth::id())->first();

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'No employee record found for your account.');
        }

        $payslips = PayrollRecord::where('employeeID', $employee->employeeID)
            ->whereHas('payrollPeriod', function ($q) {
                $q->where('status', PayrollPeriodStatus::COMPLETED);
            })
            ->with('payrollPeriod')
            ->orderByDesc(
                \App\Models\PayrollPeriod::select('payDate')
                    ->whereColumn('payroll_periods.id', 'payroll_records.payrollPeriodID')
                    ->limit(1)
            )
            ->paginate(10);

        return view('payslips.index', [
            'payslips' => $payslips,
            'employee' => $employee,
        ]);
    }

    public function show(PayrollRecord $payrollRecord)
    {
        $employee = Employee::where('userID', Auth::id())->first();

        if (!$employee || $payrollRecord->employeeID !== $employee->employeeID) {
            abort(403);
        }

        $payrollRecord->load(['payrollPeriod', 'payrollDeductions.deductionType', 'payrollDeductions.employeeLoan', 'employee.user']);

        return view('payslips.show', [
            'record' => $payrollRecord,
            'period' => $payrollRecord->payrollPeriod,
            'employee' => $payrollRecord->employee,
        ]);
    }
}
