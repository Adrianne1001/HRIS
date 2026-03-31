<?php

namespace App\Http\Controllers;

use App\Enums\LoanType;
use App\Models\Employee;
use App\Models\EmployeeLoan;
use Illuminate\Http\Request;

class EmployeeLoanController extends Controller
{
    public function index(Request $request)
    {
        $query = EmployeeLoan::with('employee.user');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee.user', function ($q) use ($search) {
                $q->where('firstName', 'like', "%{$search}%")
                  ->orWhere('lastName', 'like', "%{$search}%");
            });
        }

        if ($request->filled('loanType')) {
            $query->where('loanType', $request->loanType);
        }

        if ($request->filled('active')) {
            $query->where('isActive', $request->active === 'yes');
        }

        $loans = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        return view('employee-loans.index', [
            'loans' => $loans,
            'loanTypes' => LoanType::cases(),
        ]);
    }

    public function create()
    {
        return view('employee-loans.create', [
            'loanTypes' => LoanType::cases(),
            'employees' => Employee::with('user')->orderBy('employeeID')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employeeID' => ['required', 'exists:employees,employeeID'],
            'loanType' => ['required', 'in:SSS Salary Loan,SSS Calamity Loan,Pag-IBIG Multi-Purpose Loan,Pag-IBIG Calamity Loan,Company Loan'],
            'referenceNbr' => ['nullable', 'string', 'max:100'],
            'loanAmount' => ['required', 'numeric', 'min:0'],
            'monthlyAmortization' => ['required', 'numeric', 'min:0'],
            'startDate' => ['required', 'date'],
            'endDate' => ['nullable', 'date', 'after:startDate'],
            'remarks' => ['nullable', 'string'],
        ]);

        EmployeeLoan::create([
            ...$validated,
            'totalPaid' => 0,
            'remainingBalance' => $validated['loanAmount'],
            'isActive' => true,
        ]);

        return redirect()->route('employee-loans.index')->with('success', 'Employee loan created successfully.');
    }

    public function show(EmployeeLoan $employeeLoan)
    {
        $employeeLoan->load(['employee.user', 'payrollDeductions.payrollRecord.payrollPeriod']);

        return view('employee-loans.show', [
            'loan' => $employeeLoan,
        ]);
    }

    public function edit(EmployeeLoan $employeeLoan)
    {
        $employeeLoan->load('employee.user');

        return view('employee-loans.edit', [
            'loan' => $employeeLoan,
            'loanTypes' => LoanType::cases(),
            'employees' => Employee::with('user')->orderBy('employeeID')->get(),
        ]);
    }

    public function update(Request $request, EmployeeLoan $employeeLoan)
    {
        $validated = $request->validate([
            'referenceNbr' => ['nullable', 'string', 'max:100'],
            'loanAmount' => ['required', 'numeric', 'min:0'],
            'monthlyAmortization' => ['required', 'numeric', 'min:0'],
            'startDate' => ['required', 'date'],
            'endDate' => ['nullable', 'date', 'after:startDate'],
            'isActive' => ['boolean'],
            'remarks' => ['nullable', 'string'],
        ]);

        $validated['isActive'] = $request->boolean('isActive', $employeeLoan->isActive);
        $validated['remainingBalance'] = $validated['loanAmount'] - (float) $employeeLoan->totalPaid;

        $employeeLoan->update($validated);

        return redirect()->route('employee-loans.show', $employeeLoan)->with('success', 'Employee loan updated successfully.');
    }

    public function destroy(EmployeeLoan $employeeLoan)
    {
        if ($employeeLoan->payrollDeductions()->exists()) {
            return redirect()->route('employee-loans.show', $employeeLoan)
                ->with('error', 'Cannot delete loan with existing payroll deductions.');
        }

        $employeeLoan->delete();

        return redirect()->route('employee-loans.index')->with('success', 'Employee loan deleted successfully.');
    }
}
