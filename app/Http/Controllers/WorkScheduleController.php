<?php

namespace App\Http\Controllers;

use App\Models\WorkSchedule;
use Illuminate\Http\Request;

class WorkScheduleController extends Controller
{
    /**
     * Display a listing of the work schedules.
     */
    public function index(Request $request)
    {
        $query = WorkSchedule::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $workSchedules = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('work-schedules.index', [
            'workSchedules' => $workSchedules,
        ]);
    }

    /**
     * Show the form for creating a new work schedule.
     */
    public function create()
    {
        return view('work-schedules.create');
    }

    /**
     * Store a newly created work schedule in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'startTime' => ['required', 'date_format:H:i'],
            'endTime' => ['required', 'date_format:H:i'],
            'startBreakTime' => ['nullable', 'date_format:H:i'],
            'endBreakTime' => ['nullable', 'date_format:H:i'],
            'workingDays' => ['required', 'array', 'min:1'],
            'workingDays.*' => ['string', 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'isDefault' => ['nullable', 'boolean'],
        ]);

        // Convert working days array to comma-separated string
        $validated['workingDays'] = implode(',', $validated['workingDays']);

        // Calculate total work hours
        $validated['totalWorkHours'] = WorkSchedule::calculateTotalWorkHours(
            $validated['startTime'],
            $validated['endTime'],
            $validated['startBreakTime'] ?? null,
            $validated['endBreakTime'] ?? null
        );

        // If setting as default, clear other defaults first
        if (!empty($validated['isDefault'])) {
            WorkSchedule::where('isDefault', true)->update(['isDefault' => false]);
        }

        $validated['isDefault'] = !empty($validated['isDefault']);

        WorkSchedule::create($validated);

        return redirect()->route('work-schedules.index')->with('success', 'Work schedule created successfully.');
    }

    /**
     * Display the specified work schedule.
     */
    public function show(WorkSchedule $workSchedule)
    {
        $workSchedule->load('employees.user');
        
        return view('work-schedules.show', [
            'workSchedule' => $workSchedule,
        ]);
    }

    /**
     * Show the form for editing the specified work schedule.
     */
    public function edit(WorkSchedule $workSchedule)
    {
        return view('work-schedules.edit', [
            'workSchedule' => $workSchedule,
        ]);
    }

    /**
     * Update the specified work schedule in storage.
     */
    public function update(Request $request, WorkSchedule $workSchedule)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'startTime' => ['required', 'date_format:H:i'],
            'endTime' => ['required', 'date_format:H:i'],
            'startBreakTime' => ['nullable', 'date_format:H:i'],
            'endBreakTime' => ['nullable', 'date_format:H:i'],
            'workingDays' => ['required', 'array', 'min:1'],
            'workingDays.*' => ['string', 'in:Mon,Tue,Wed,Thu,Fri,Sat,Sun'],
            'isDefault' => ['nullable', 'boolean'],
        ]);

        // Convert working days array to comma-separated string
        $validated['workingDays'] = implode(',', $validated['workingDays']);

        // Calculate total work hours
        $validated['totalWorkHours'] = WorkSchedule::calculateTotalWorkHours(
            $validated['startTime'],
            $validated['endTime'],
            $validated['startBreakTime'] ?? null,
            $validated['endBreakTime'] ?? null
        );

        // If setting as default, clear other defaults first
        if (!empty($validated['isDefault'])) {
            WorkSchedule::where('isDefault', true)->where('id', '!=', $workSchedule->id)->update(['isDefault' => false]);
        }

        $validated['isDefault'] = !empty($validated['isDefault']);

        $workSchedule->update($validated);

        return redirect()->route('work-schedules.index')->with('success', 'Work schedule updated successfully.');
    }

    /**
     * Set the specified work schedule as default.
     */
    public function setDefault(WorkSchedule $workSchedule)
    {
        $workSchedule->setAsDefault();

        return redirect()->route('work-schedules.index')->with('success', "'{$workSchedule->name}' is now the default work schedule.");
    }

    /**
     * Remove the specified work schedule from storage.
     */
    public function destroy(WorkSchedule $workSchedule)
    {
        // Check if any employees are using this schedule
        if ($workSchedule->employees()->count() > 0) {
            return redirect()->route('work-schedules.index')
                ->with('error', 'Cannot delete this work schedule. It is currently assigned to ' . $workSchedule->employees()->count() . ' employee(s).');
        }

        $workSchedule->delete();

        return redirect()->route('work-schedules.index')->with('success', 'Work schedule deleted successfully.');
    }
}
