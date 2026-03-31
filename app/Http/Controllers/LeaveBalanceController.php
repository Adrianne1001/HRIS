<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveBalanceController extends Controller
{
    public function index()
    {
        $employee = Employee::where('userID', Auth::id())->first();

        if (!$employee) {
            return redirect()->route('dashboard')
                ->with('error', 'No employee record found for your account.');
        }

        $balances = LeaveBalance::with('leaveType')
            ->where('employeeID', $employee->employeeID)
            ->where('year', now()->year)
            ->get();

        return view('leave-balances.index', [
            'balances' => $balances,
            'employee' => $employee,
            'year' => now()->year,
        ]);
    }

    public function manage(Request $request)
    {
        $year = $request->get('year', now()->year);

        $query = Employee::with(['user', 'leaveBalances' => function ($q) use ($year) {
            $q->with('leaveType')->where('year', $year);
        }]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('firstName', 'like', "%{$search}%")
                  ->orWhere('lastName', 'like', "%{$search}%");
            });
        }

        $employees = $query->orderBy('employeeID', 'desc')->paginate(10)->withQueryString();
        $leaveTypes = LeaveType::where('isActive', true)->orderBy('name')->get();

        return view('leave-balances.manage', [
            'employees' => $employees,
            'leaveTypes' => $leaveTypes,
            'year' => $year,
        ]);
    }

    public function allocate(Request $request)
    {
        $validated = $request->validate([
            'year' => ['required', 'integer', 'min:2020', 'max:2099'],
        ]);

        $year = $validated['year'];
        $employees = Employee::all();
        $leaveTypes = LeaveType::where('isActive', true)->get();
        $count = 0;

        DB::transaction(function () use ($employees, $leaveTypes, $year, &$count) {
            foreach ($employees as $employee) {
                foreach ($leaveTypes as $leaveType) {
                    // Skip gender-restricted leave types that don't match
                    if ($leaveType->gender !== null && $leaveType->gender !== $employee->gender) {
                        continue;
                    }

                    LeaveBalance::updateOrCreate(
                        [
                            'employeeID' => $employee->employeeID,
                            'leaveTypeID' => $leaveType->id,
                            'year' => $year,
                        ],
                        [
                            'totalCredits' => $leaveType->defaultCredits,
                        ]
                    );

                    $count++;
                }
            }
        });

        return redirect()->route('leave-balances.manage', ['year' => $year])
            ->with('success', "Successfully allocated leave credits. {$count} balance(s) created/updated.");
    }
}
