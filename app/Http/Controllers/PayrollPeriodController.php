<?php

namespace App\Http\Controllers;

use App\Enums\EmploymentStatus;
use App\Enums\PayrollPeriodStatus;
use App\Enums\PayrollPeriodType;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use App\Models\PayrollDeduction;
use App\Models\PayrollPeriod;
use App\Models\PayrollRecord;
use App\Services\PayrollComputationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollPeriodController extends Controller
{
    public function index(Request $request)
    {
        $query = PayrollPeriod::query();

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->filled('year')) {
            $query->whereYear('startDate', $request->year);
        }

        $periods = $query->orderBy('startDate', 'desc')->paginate(10)->withQueryString();

        $counts = [
            'all' => PayrollPeriod::count(),
            'Draft' => PayrollPeriod::where('status', 'Draft')->count(),
            'Processing' => PayrollPeriod::where('status', 'Processing')->count(),
            'Completed' => PayrollPeriod::where('status', 'Completed')->count(),
        ];

        return view('payroll.index', [
            'periods' => $periods,
            'counts' => $counts,
            'currentStatus' => $request->get('status', 'all'),
            'periodTypes' => PayrollPeriodType::cases(),
            'periodStatuses' => PayrollPeriodStatus::cases(),
        ]);
    }

    public function create()
    {
        return view('payroll.create', [
            'periodTypes' => PayrollPeriodType::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'periodType' => ['required', 'in:First Half,Second Half'],
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:startDate'],
            'payDate' => ['required', 'date', 'after_or_equal:endDate'],
        ]);

        PayrollPeriod::create([
            ...$validated,
            'status' => PayrollPeriodStatus::DRAFT,
        ]);

        return redirect()->route('payroll.index')->with('success', 'Payroll period created successfully.');
    }

    public function show(PayrollPeriod $payrollPeriod)
    {
        // NOTE: The route model binding uses 'payroll' parameter name from resource route.
        // We need to load the period's records with employee data.
        $payrollPeriod->load(['payrollRecords.employee.user']);

        $records = $payrollPeriod->payrollRecords()->with('employee.user')->paginate(15);

        // Government contribution summary
        $contributionSummary = null;
        if ($payrollPeriod->payrollRecords->isNotEmpty()) {
            $contributionSummary = [
                'sssEmployee' => $payrollPeriod->payrollRecords->sum('sssEmployee'),
                'sssEmployer' => $payrollPeriod->payrollRecords->sum('sssEmployer'),
                'sssEC' => $payrollPeriod->payrollRecords->sum('sssEC'),
                'philhealthEmployee' => $payrollPeriod->payrollRecords->sum('philhealthEmployee'),
                'philhealthEmployer' => $payrollPeriod->payrollRecords->sum('philhealthEmployer'),
                'pagibigEmployee' => $payrollPeriod->payrollRecords->sum('pagibigEmployee'),
                'pagibigEmployer' => $payrollPeriod->payrollRecords->sum('pagibigEmployer'),
            ];
        }

        return view('payroll.show', [
            'period' => $payrollPeriod,
            'records' => $records,
            'contributionSummary' => $contributionSummary,
        ]);
    }

    public function process(PayrollPeriod $payrollPeriod, PayrollComputationService $service)
    {
        if ($payrollPeriod->status !== PayrollPeriodStatus::DRAFT) {
            return redirect()->route('payroll.show', $payrollPeriod)
                ->with('error', 'Only draft periods can be processed.');
        }

        DB::transaction(function () use ($payrollPeriod, $service) {
            $payrollPeriod->update([
                'status' => PayrollPeriodStatus::PROCESSING,
                'processedAt' => now(),
            ]);

            $employees = Employee::where('employmentStatus', EmploymentStatus::ACTIVE)
                ->whereNotNull('workScheduleID')
                ->with('workSchedule')
                ->get();

            $totalGross = 0;
            $totalDeductions = 0;
            $totalNet = 0;
            $totalEmployerContrib = 0;

            foreach ($employees as $employee) {
                // Compute earnings
                $earnings = $service->computePayrollForEmployee($employee, $payrollPeriod);

                // Create payroll record
                $record = PayrollRecord::create([
                    'payrollPeriodID' => $payrollPeriod->id,
                    'employeeID' => $employee->employeeID,
                    ...$earnings,
                ]);

                // Compute mandatory deductions (2nd cutoff only)
                $mandatory = $service->computeMandatoryDeductions($record, $payrollPeriod);
                $record->update($mandatory);

                // Create PayrollDeduction entries for mandatory deductions
                if ($mandatory['sssEmployee'] > 0) {
                    PayrollDeduction::create([
                        'payrollRecordID' => $record->id,
                        'deductionTypeID' => \App\Models\DeductionType::where('code', 'SSS')->value('id'),
                        'description' => 'SSS Employee Share',
                        'employeeAmount' => $mandatory['sssEmployee'],
                        'employerAmount' => $mandatory['sssEmployer'] + $mandatory['sssEC'],
                    ]);
                }

                if ($mandatory['philhealthEmployee'] > 0) {
                    PayrollDeduction::create([
                        'payrollRecordID' => $record->id,
                        'deductionTypeID' => \App\Models\DeductionType::where('code', 'PHIC')->value('id'),
                        'description' => 'PhilHealth Employee Share',
                        'employeeAmount' => $mandatory['philhealthEmployee'],
                        'employerAmount' => $mandatory['philhealthEmployer'],
                    ]);
                }

                if ($mandatory['pagibigEmployee'] > 0) {
                    PayrollDeduction::create([
                        'payrollRecordID' => $record->id,
                        'deductionTypeID' => \App\Models\DeductionType::where('code', 'HDMF')->value('id'),
                        'description' => 'Pag-IBIG Employee Share',
                        'employeeAmount' => $mandatory['pagibigEmployee'],
                        'employerAmount' => $mandatory['pagibigEmployer'],
                    ]);
                }

                if ($mandatory['withholdingTax'] > 0) {
                    PayrollDeduction::create([
                        'payrollRecordID' => $record->id,
                        'deductionTypeID' => \App\Models\DeductionType::where('code', 'TAX')->value('id'),
                        'description' => 'Withholding Tax',
                        'employeeAmount' => $mandatory['withholdingTax'],
                        'employerAmount' => 0,
                    ]);
                }

                // Compute loan deductions
                $loanDeductions = $service->computeLoanDeductions($employee, $payrollPeriod);
                $totalLoanDeductions = 0;
                foreach ($loanDeductions as $loanId => $amount) {
                    $loan = EmployeeLoan::find($loanId);
                    PayrollDeduction::create([
                        'payrollRecordID' => $record->id,
                        'employeeLoanID' => $loanId,
                        'description' => $loan->loanType->value,
                        'employeeAmount' => $amount,
                        'employerAmount' => 0,
                    ]);

                    // Update loan balance
                    $loan->increment('totalPaid', $amount);
                    $loan->decrement('remainingBalance', $amount);

                    if ($loan->remainingBalance <= 0) {
                        $loan->update(['isActive' => false]);
                    }

                    $totalLoanDeductions += $amount;
                }

                // Update record totals
                $totalMandatory = $mandatory['sssEmployee'] + $mandatory['philhealthEmployee']
                    + $mandatory['pagibigEmployee'] + $mandatory['withholdingTax'];
                $recordTotalDeductions = $totalMandatory + $totalLoanDeductions;
                $netPay = (float) $record->grossPay - $recordTotalDeductions;

                $record->update([
                    'totalMandatoryDeductions' => round($totalMandatory, 2),
                    'totalLoanDeductions' => round($totalLoanDeductions, 2),
                    'totalDeductions' => round($recordTotalDeductions, 2),
                    'netPay' => round($netPay, 2),
                ]);

                $employerContrib = $mandatory['sssEmployer'] + $mandatory['sssEC']
                    + $mandatory['philhealthEmployer'] + $mandatory['pagibigEmployer'];

                $totalGross += (float) $record->grossPay;
                $totalDeductions += $recordTotalDeductions;
                $totalNet += $netPay;
                $totalEmployerContrib += $employerContrib;
            }

            $payrollPeriod->update([
                'totalGrossPay' => round($totalGross, 2),
                'totalDeductions' => round($totalDeductions, 2),
                'totalNetPay' => round($totalNet, 2),
                'totalEmployerContributions' => round($totalEmployerContrib, 2),
                'employeeCount' => $employees->count(),
            ]);
        });

        return redirect()->route('payroll.show', $payrollPeriod)
            ->with('success', 'Payroll processed successfully for ' . $payrollPeriod->employeeCount . ' employees.');
    }

    public function complete(PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->status !== PayrollPeriodStatus::PROCESSING) {
            return redirect()->route('payroll.show', $payrollPeriod)
                ->with('error', 'Only processing periods can be completed.');
        }

        DB::transaction(function () use ($payrollPeriod) {
            $payrollPeriod->update([
                'status' => PayrollPeriodStatus::COMPLETED,
                'completedAt' => now(),
            ]);
        });

        return redirect()->route('payroll.show', $payrollPeriod)
            ->with('success', 'Payroll period marked as completed.');
    }

    public function destroy(PayrollPeriod $payrollPeriod)
    {
        if ($payrollPeriod->status !== PayrollPeriodStatus::DRAFT) {
            return redirect()->route('payroll.index')
                ->with('error', 'Only draft periods can be deleted.');
        }

        $payrollPeriod->delete();

        return redirect()->route('payroll.index')
            ->with('success', 'Payroll period deleted successfully.');
    }
}
