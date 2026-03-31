<?php

namespace App\Http\Controllers;

use App\Enums\LeaveStatus;
use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LeaveRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveRequest::with(['employee.user', 'leaveType']);

        // Status filter tabs
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Search by employee name
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee.user', function ($q) use ($search) {
                $q->where('firstName', 'like', "%{$search}%")
                  ->orWhere('lastName', 'like', "%{$search}%");
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->where('startDate', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('endDate', '<=', $request->date_to);
        }

        $leaveRequests = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        // Count by status for tabs
        $counts = [
            'all' => LeaveRequest::count(),
            'Pending' => LeaveRequest::where('status', 'Pending')->count(),
            'Approved' => LeaveRequest::where('status', 'Approved')->count(),
            'Rejected' => LeaveRequest::where('status', 'Rejected')->count(),
            'Cancelled' => LeaveRequest::where('status', 'Cancelled')->count(),
        ];

        return view('leave-requests.index', [
            'leaveRequests' => $leaveRequests,
            'counts' => $counts,
            'currentStatus' => $request->get('status', 'all'),
        ]);
    }

    public function create()
    {
        $employee = Employee::where('userID', Auth::id())->first();

        if (!$employee) {
            return redirect()->route('leave-requests.index')
                ->with('error', 'No employee record found for your account.');
        }

        $leaveTypes = LeaveType::where('isActive', true)->get();

        // Filter leave types by gender eligibility
        $leaveTypes = $leaveTypes->filter(function ($type) use ($employee) {
            return $type->gender === null || $type->gender === $employee->gender;
        });

        // Get balances for current year
        $balances = LeaveBalance::where('employeeID', $employee->employeeID)
            ->where('year', now()->year)
            ->get()
            ->keyBy('leaveTypeID');

        return view('leave-requests.create', [
            'employee' => $employee,
            'leaveTypes' => $leaveTypes,
            'balances' => $balances,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'leaveTypeID' => ['required', 'exists:leave_types,id'],
            'startDate' => ['required', 'date', 'after_or_equal:today'],
            'endDate' => ['required', 'date', 'after_or_equal:startDate'],
            'isHalfDay' => ['boolean'],
            'halfDayPeriod' => ['nullable', 'required_if:isHalfDay,true', 'in:AM,PM'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $employee = Employee::where('userID', Auth::id())->firstOrFail();
        $leaveType = LeaveType::findOrFail($validated['leaveTypeID']);
        $isHalfDay = $request->boolean('isHalfDay');

        // Validate leave type is active
        if (!$leaveType->isActive) {
            return back()->withErrors(['leaveTypeID' => 'This leave type is no longer active.'])->withInput();
        }

        // Validate gender eligibility
        if ($leaveType->gender !== null && $leaveType->gender !== $employee->gender) {
            return back()->withErrors(['leaveTypeID' => 'You are not eligible for this leave type.'])->withInput();
        }

        // Half-day: startDate must equal endDate
        if ($isHalfDay && $validated['startDate'] !== $validated['endDate']) {
            return back()->withErrors(['endDate' => 'For half-day leave, start and end date must be the same.'])->withInput();
        }

        // Calculate total days
        $startDate = Carbon::parse($validated['startDate']);
        $endDate = Carbon::parse($validated['endDate']);

        if ($isHalfDay) {
            $totalDays = 0.5;
        } else {
            $totalDays = $this->countBusinessDays($startDate, $endDate);
        }

        if ($totalDays <= 0) {
            return back()->withErrors(['startDate' => 'The selected date range contains no business days.'])->withInput();
        }

        // Check max consecutive days
        if ($leaveType->maxConsecutiveDays && $totalDays > $leaveType->maxConsecutiveDays) {
            return back()->withErrors(['endDate' => "This leave type allows a maximum of {$leaveType->maxConsecutiveDays} consecutive days."])->withInput();
        }

        // Check for overlapping requests
        $overlap = LeaveRequest::where('employeeID', $employee->employeeID)
            ->where('status', '!=', LeaveStatus::CANCELLED)
            ->where(function ($q) use ($startDate, $endDate) {
                $q->where(function ($q2) use ($startDate, $endDate) {
                    $q2->where('startDate', '<=', $endDate)
                        ->where('endDate', '>=', $startDate);
                });
            })
            ->exists();

        if ($overlap) {
            return back()->withErrors(['startDate' => 'You already have a leave request that overlaps with these dates.'])->withInput();
        }

        // Check balance
        $balance = LeaveBalance::where('employeeID', $employee->employeeID)
            ->where('leaveTypeID', $leaveType->id)
            ->where('year', $startDate->year)
            ->first();

        if (!$balance) {
            return back()->withErrors(['leaveTypeID' => 'No leave balance found for this leave type for the current year.'])->withInput();
        }

        if ($balance->remainingCredits < $totalDays) {
            return back()->withErrors(['leaveTypeID' => "Insufficient leave balance. Available: {$balance->remainingCredits} days, Requested: {$totalDays} days."])->withInput();
        }

        DB::transaction(function () use ($validated, $employee, $leaveType, $totalDays, $isHalfDay, $balance) {
            LeaveRequest::create([
                'employeeID' => $employee->employeeID,
                'leaveTypeID' => $leaveType->id,
                'startDate' => $validated['startDate'],
                'endDate' => $validated['endDate'],
                'totalDays' => $totalDays,
                'isHalfDay' => $isHalfDay,
                'halfDayPeriod' => $isHalfDay ? $validated['halfDayPeriod'] : null,
                'reason' => $validated['reason'],
                'status' => LeaveStatus::PENDING,
            ]);

            $balance->increment('pendingCredits', $totalDays);
        });

        return redirect()->route('leave-requests.index')->with('success', 'Leave request submitted successfully.');
    }

    public function show(LeaveRequest $leaveRequest)
    {
        $leaveRequest->load(['employee.user', 'leaveType', 'approvedBy']);

        return view('leave-requests.show', [
            'leaveRequest' => $leaveRequest,
        ]);
    }

    public function approve(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveStatus::PENDING) {
            return redirect()->route('leave-requests.show', $leaveRequest)
                ->with('error', 'Only pending requests can be approved.');
        }

        DB::transaction(function () use ($leaveRequest) {
            $leaveRequest->update([
                'status' => LeaveStatus::APPROVED,
                'approvedByID' => Auth::id(),
                'approvedAt' => now(),
            ]);

            // Update balance: move from pending to used
            $balance = LeaveBalance::where('employeeID', $leaveRequest->employeeID)
                ->where('leaveTypeID', $leaveRequest->leaveTypeID)
                ->where('year', $leaveRequest->startDate->year)
                ->firstOrFail();

            $balance->decrement('pendingCredits', $leaveRequest->totalDays);
            $balance->increment('usedCredits', $leaveRequest->totalDays);

            // Create AttendanceRecord entries for each business day
            $this->createAttendanceRecords($leaveRequest);
        });

        return redirect()->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request approved successfully.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveStatus::PENDING) {
            return redirect()->route('leave-requests.show', $leaveRequest)
                ->with('error', 'Only pending requests can be rejected.');
        }

        $validated = $request->validate([
            'rejectionReason' => ['required', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($leaveRequest, $validated) {
            $leaveRequest->update([
                'status' => LeaveStatus::REJECTED,
                'approvedByID' => Auth::id(),
                'approvedAt' => now(),
                'rejectionReason' => $validated['rejectionReason'],
            ]);

            // Release pending credits
            $balance = LeaveBalance::where('employeeID', $leaveRequest->employeeID)
                ->where('leaveTypeID', $leaveRequest->leaveTypeID)
                ->where('year', $leaveRequest->startDate->year)
                ->firstOrFail();

            $balance->decrement('pendingCredits', $leaveRequest->totalDays);
        });

        return redirect()->route('leave-requests.show', $leaveRequest)
            ->with('success', 'Leave request rejected.');
    }

    public function cancel(LeaveRequest $leaveRequest)
    {
        if ($leaveRequest->status !== LeaveStatus::PENDING) {
            return redirect()->route('leave-requests.show', $leaveRequest)
                ->with('error', 'Only pending requests can be cancelled.');
        }

        DB::transaction(function () use ($leaveRequest) {
            $leaveRequest->update([
                'status' => LeaveStatus::CANCELLED,
            ]);

            // Release pending credits
            $balance = LeaveBalance::where('employeeID', $leaveRequest->employeeID)
                ->where('leaveTypeID', $leaveRequest->leaveTypeID)
                ->where('year', $leaveRequest->startDate->year)
                ->firstOrFail();

            $balance->decrement('pendingCredits', $leaveRequest->totalDays);
        });

        return redirect()->route('leave-requests.index')
            ->with('success', 'Leave request cancelled.');
    }

    private function countBusinessDays(Carbon $start, Carbon $end): int
    {
        $days = 0;
        $current = $start->copy();

        while ($current->lte($end)) {
            if ($current->isWeekday()) {
                $days++;
            }
            $current->addDay();
        }

        return $days;
    }

    private function createAttendanceRecords(LeaveRequest $leaveRequest): void
    {
        $employee = $leaveRequest->employee;
        $workSchedule = $employee->workSchedule;

        // Map leave type code to remarks
        $remarksMap = [
            'VL' => AttendanceRecord::REMARKS_VACATION_LEAVE,
            'SL' => AttendanceRecord::REMARKS_SICK_LEAVE,
        ];

        $remarks = $remarksMap[$leaveRequest->leaveType->code] ?? AttendanceRecord::REMARKS_VACATION_LEAVE;

        $current = $leaveRequest->startDate->copy();

        while ($current->lte($leaveRequest->endDate)) {
            if ($current->isWeekday()) {
                // Build shift times for this date
                $shiftTimeIn = $workSchedule 
                    ? Carbon::parse($current->format('Y-m-d') . ' ' . $workSchedule->startTime)
                    : Carbon::parse($current->format('Y-m-d') . ' 08:00:00');
                $shiftTimeOut = $workSchedule 
                    ? Carbon::parse($current->format('Y-m-d') . ' ' . $workSchedule->endTime)
                    : Carbon::parse($current->format('Y-m-d') . ' 17:00:00');

                AttendanceRecord::create([
                    'employee_id' => $employee->employeeID,
                    'workDate' => $current->format('Y-m-d'),
                    'shiftTimeIn' => $shiftTimeIn,
                    'shiftTimeOut' => $shiftTimeOut,
                    'actualTimeIn' => null,
                    'actualTimeOut' => null,
                    'hoursWorked' => 0,
                    'remarks' => $remarks,
                ]);
            }
            $current->addDay();
        }
    }
}
