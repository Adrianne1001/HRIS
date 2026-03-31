<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveType::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $leaveTypes = $query->orderBy('name')->paginate(10)->withQueryString();

        return view('leave-types.index', [
            'leaveTypes' => $leaveTypes,
        ]);
    }

    public function create()
    {
        return view('leave-types.create', [
            'genders' => Gender::cases(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:leave_types,code'],
            'defaultCredits' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'description' => ['nullable', 'string'],
            'isActive' => ['boolean'],
            'isPaid' => ['boolean'],
            'requiresDocument' => ['boolean'],
            'maxConsecutiveDays' => ['nullable', 'integer', 'min:1'],
            'gender' => ['nullable', 'string', 'in:Male,Female'],
        ]);

        // Set boolean defaults for unchecked checkboxes
        $validated['isActive'] = $request->boolean('isActive');
        $validated['isPaid'] = $request->boolean('isPaid');
        $validated['requiresDocument'] = $request->boolean('requiresDocument');

        LeaveType::create($validated);

        return redirect()->route('leave-types.index')->with('success', 'Leave type created successfully.');
    }

    public function show(LeaveType $leaveType)
    {
        return view('leave-types.show', [
            'leaveType' => $leaveType,
        ]);
    }

    public function edit(LeaveType $leaveType)
    {
        return view('leave-types.edit', [
            'leaveType' => $leaveType,
            'genders' => Gender::cases(),
        ]);
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:leave_types,code,' . $leaveType->id],
            'defaultCredits' => ['required', 'numeric', 'min:0', 'max:999.99'],
            'description' => ['nullable', 'string'],
            'isActive' => ['boolean'],
            'isPaid' => ['boolean'],
            'requiresDocument' => ['boolean'],
            'maxConsecutiveDays' => ['nullable', 'integer', 'min:1'],
            'gender' => ['nullable', 'string', 'in:Male,Female'],
        ]);

        $validated['isActive'] = $request->boolean('isActive');
        $validated['isPaid'] = $request->boolean('isPaid');
        $validated['requiresDocument'] = $request->boolean('requiresDocument');

        $leaveType->update($validated);

        return redirect()->route('leave-types.index')->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        if ($leaveType->leaveRequests()->count() > 0) {
            return redirect()->route('leave-types.index')
                ->with('error', 'Cannot delete this leave type. It has ' . $leaveType->leaveRequests()->count() . ' associated leave request(s). Deactivate it instead.');
        }

        $leaveType->delete();

        return redirect()->route('leave-types.index')->with('success', 'Leave type deleted successfully.');
    }
}
