<?php

namespace App\Http\Controllers;

use App\Enums\Department;
use App\Enums\EmploymentStatus;
use App\Enums\EmploymentType;
use App\Enums\Gender;
use App\Enums\MaritalStatus;
use App\Enums\Position;
use App\Models\Employee;
use App\Models\User;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index(Request $request)
    {
        $query = Employee::with('user');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('firstName', 'like', "%{$search}%")
                  ->orWhere('lastName', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Filter by employment status
        if ($request->filled('status')) {
            $query->where('employmentStatus', $request->status);
        }

        // Filter by employment type
        if ($request->filled('type')) {
            $query->where('employmentType', $request->type);
        }

        $employees = $query->orderBy('employeeID', 'desc')->paginate(10)->withQueryString();

        return view('employees.index', [
            'employees' => $employees,
            'departments' => Department::cases(),
            'statuses' => EmploymentStatus::cases(),
            'types' => EmploymentType::cases(),
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        $workSchedules = WorkSchedule::orderBy('name')->get();
        $defaultSchedule = WorkSchedule::getDefault();

        return view('employees.create', [
            'genders' => Gender::cases(),
            'maritalStatuses' => MaritalStatus::cases(),
            'departments' => Department::cases(),
            'positions' => Position::cases(),
            'employmentStatuses' => EmploymentStatus::cases(),
            'employmentTypes' => EmploymentType::cases(),
            'workSchedules' => $workSchedules,
            'defaultScheduleId' => $defaultSchedule?->id,
        ]);
    }

    /**
     * Store a newly created employee in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // User fields
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:191', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            
            // Employee fields
            'dateOfBirth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'string'],
            'maritalStatus' => ['required', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'phoneNbr' => ['required', 'string', 'max:20'],
            'hireDate' => ['required', 'date'],
            'employmentStatus' => ['required', 'string'],
            'employmentType' => ['required', 'string'],
            'department' => ['required', 'string'],
            'jobTitle' => ['required', 'string'],
            'basicMonthlySalary' => ['required', 'numeric', 'min:0'],
            'workScheduleID' => ['nullable', 'exists:work_schedules,id'],
            'emergencyContactName' => ['nullable', 'string', 'max:255'],
            'emergencyContactPhoneNbr' => ['nullable', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($validated) {
            // Create the user account
            $user = User::create([
                'firstName' => $validated['firstName'],
                'middleName' => $validated['middleName'],
                'lastName' => $validated['lastName'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Create the employee record
            Employee::create([
                'userID' => $user->id,
                'dateOfBirth' => $validated['dateOfBirth'],
                'gender' => $validated['gender'],
                'maritalStatus' => $validated['maritalStatus'],
                'address' => $validated['address'],
                'phoneNbr' => $validated['phoneNbr'],
                'hireDate' => $validated['hireDate'],
                'employmentStatus' => $validated['employmentStatus'],
                'employmentType' => $validated['employmentType'],
                'department' => $validated['department'],
                'jobTitle' => $validated['jobTitle'],
                'basicMonthlySalary' => $validated['basicMonthlySalary'],
                'workScheduleID' => $validated['workScheduleID'] ?? null,
                'emergencyContactName' => $validated['emergencyContactName'],
                'emergencyContactPhoneNbr' => $validated['emergencyContactPhoneNbr'],
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified employee.
     */
    public function show(Employee $employee)
    {
        $employee->load(['user', 'workSchedule']);
        
        return view('employees.show', [
            'employee' => $employee,
        ]);
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $employee->load('user');

        return view('employees.edit', [
            'employee' => $employee,
            'genders' => Gender::cases(),
            'maritalStatuses' => MaritalStatus::cases(),
            'departments' => Department::cases(),
            'positions' => Position::cases(),
            'employmentStatuses' => EmploymentStatus::cases(),
            'employmentTypes' => EmploymentType::cases(),
            'workSchedules' => WorkSchedule::orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified employee in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            // User fields
            'firstName' => ['required', 'string', 'max:255'],
            'middleName' => ['nullable', 'string', 'max:255'],
            'lastName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:191', 'unique:users,email,' . $employee->userID],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            
            // Employee fields
            'dateOfBirth' => ['required', 'date', 'before:today'],
            'gender' => ['required', 'string'],
            'maritalStatus' => ['required', 'string'],
            'address' => ['required', 'string', 'max:500'],
            'phoneNbr' => ['required', 'string', 'max:20'],
            'hireDate' => ['required', 'date'],
            'employmentStatus' => ['required', 'string'],
            'employmentType' => ['required', 'string'],
            'department' => ['required', 'string'],
            'jobTitle' => ['required', 'string'],
            'basicMonthlySalary' => ['required', 'numeric', 'min:0'],
            'workScheduleID' => ['nullable', 'exists:work_schedules,id'],
            'emergencyContactName' => ['nullable', 'string', 'max:255'],
            'emergencyContactPhoneNbr' => ['nullable', 'string', 'max:20'],
        ]);

        DB::transaction(function () use ($validated, $employee) {
            // Update user account
            $userData = [
                'firstName' => $validated['firstName'],
                'middleName' => $validated['middleName'],
                'lastName' => $validated['lastName'],
                'email' => $validated['email'],
            ];

            if (!empty($validated['password'])) {
                $userData['password'] = Hash::make($validated['password']);
            }

            $employee->user->update($userData);

            // Update employee record
            $employee->update([
                'dateOfBirth' => $validated['dateOfBirth'],
                'gender' => $validated['gender'],
                'maritalStatus' => $validated['maritalStatus'],
                'address' => $validated['address'],
                'phoneNbr' => $validated['phoneNbr'],
                'hireDate' => $validated['hireDate'],
                'employmentStatus' => $validated['employmentStatus'],
                'employmentType' => $validated['employmentType'],
                'department' => $validated['department'],
                'jobTitle' => $validated['jobTitle'],
                'basicMonthlySalary' => $validated['basicMonthlySalary'],
                'workScheduleID' => $validated['workScheduleID'] ?? null,
                'emergencyContactName' => $validated['emergencyContactName'],
                'emergencyContactPhoneNbr' => $validated['emergencyContactPhoneNbr'],
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        DB::transaction(function () use ($employee) {
            $user = $employee->user;
            $employee->delete();
            $user->delete();
        });

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully.');
    }
}
