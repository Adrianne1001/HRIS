<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AttendanceRecordController extends Controller
{
    /**
     * Display the DTR (Daily Time Record) page.
     */
    public function dtr()
    {
        $user = Auth::user();
        $employee = Employee::where('userID', $user->id)->with('workSchedule')->first();

        $today = Carbon::today();
        $todayRecord = null;
        $activeRecord = null; // For graveyard shifts that started yesterday
        $recentRecords = collect();

        if ($employee) {
            // Get today's attendance record if exists
            $todayRecord = AttendanceRecord::where('employee_id', $employee->employeeID)
                ->whereDate('workDate', $today)
                ->first();

            // Check for yesterday's incomplete graveyard shift record
            // (employee timed in yesterday but hasn't timed out yet)
            if (!$todayRecord || !$todayRecord->actualTimeIn) {
                $yesterday = Carbon::yesterday();
                $activeRecord = AttendanceRecord::where('employee_id', $employee->employeeID)
                    ->whereDate('workDate', $yesterday)
                    ->whereNotNull('actualTimeIn')
                    ->whereNull('actualTimeOut')
                    ->first();
            }

            // Get recent attendance records (last 7 days)
            $recentRecords = AttendanceRecord::where('employee_id', $employee->employeeID)
                ->orderBy('workDate', 'desc')
                ->take(7)
                ->get();
        }

        return view('attendance.dtr', compact('employee', 'todayRecord', 'activeRecord', 'recentRecords', 'today'));
    }

    /**
     * Handle Time-In action.
     */
    public function timeIn(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('userID', $user->id)->with('workSchedule')->first();

        if (!$employee) {
            return back()->with('error', 'No employee record found.');
        }

        if (!$employee->workSchedule) {
            return back()->with('error', 'No work schedule assigned. Please contact HR.');
        }

        $now = Carbon::now();
        $today = $now->toDateString();

        // Check if already timed in today
        $existingRecord = AttendanceRecord::where('employee_id', $employee->employeeID)
            ->whereDate('workDate', $today)
            ->first();

        if ($existingRecord && $existingRecord->actualTimeIn) {
            return back()->with('error', 'You have already timed in today.');
        }

        $workSchedule = $employee->workSchedule;

        // Build shift start and end as full datetime
        $shiftStart = Carbon::parse($today . ' ' . $workSchedule->startTime->format('H:i:s'));
        $shiftEnd = Carbon::parse($today . ' ' . $workSchedule->endTime->format('H:i:s'));
        
        // Handle overnight shifts - if end time is before or equal to start time, add a day
        if ($shiftEnd->lessThanOrEqualTo($shiftStart)) {
            $shiftEnd->addDay();
        }

        // Create or update attendance record
        $record = $existingRecord ?? new AttendanceRecord();
        $record->employee_id = $employee->employeeID;
        $record->workDate = $today;
        $record->shiftTimeIn = $shiftStart;
        $record->shiftTimeOut = $shiftEnd;
        $record->actualTimeIn = $now;
        $record->inOrOut = AttendanceRecord::IN;

        // Calculate advance OT if timed in before shift
        if ($now->lessThan($shiftStart)) {
            $record->advanceOTHours = round($now->diffInMinutes($shiftStart) / 60, 2);
        }

        // Determine remarks (Late if after shift start)
        if ($now->greaterThan($shiftStart)) {
            $record->remarks = AttendanceRecord::REMARKS_LATE;
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('attendance', 'public');
            $record->image = $path;
        }

        $record->save();

        return back()->with('success', 'Time-In recorded successfully at ' . $now->format('h:i A'));
    }

    /**
     * Handle Time-Out action.
     */
    public function timeOut(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('userID', $user->id)->with('workSchedule')->first();

        if (!$employee) {
            return back()->with('error', 'No employee record found.');
        }

        $now = Carbon::now();
        $today = Carbon::today()->toDateString();
        $yesterday = Carbon::yesterday()->toDateString();

        // First, try to find today's record with time-in
        $record = AttendanceRecord::where('employee_id', $employee->employeeID)
            ->whereDate('workDate', $today)
            ->whereNotNull('actualTimeIn')
            ->whereNull('actualTimeOut')
            ->first();

        // If not found, check for yesterday's record (for graveyard/overnight shifts)
        if (!$record) {
            $record = AttendanceRecord::where('employee_id', $employee->employeeID)
                ->whereDate('workDate', $yesterday)
                ->whereNotNull('actualTimeIn')
                ->whereNull('actualTimeOut')
                ->first();
        }

        if (!$record) {
            return back()->with('error', 'No Time-In record found. Please time in first.');
        }

        if ($record->actualTimeOut) {
            return back()->with('error', 'You have already timed out.');
        }

        $record->actualTimeOut = $now;
        $record->inOrOut = AttendanceRecord::OUT;

        // shiftTimeIn and shiftTimeOut are now full datetime values
        $shiftStart = Carbon::parse($record->shiftTimeIn);
        $shiftEnd = Carbon::parse($record->shiftTimeOut);

        // Calculate after-shift OT hours
        if ($now->greaterThan($shiftEnd)) {
            $record->afterShiftOTHours = round($shiftEnd->diffInMinutes($now) / 60, 2);
        }

        // Calculate hours worked
        $record->hoursWorked = $this->calculateHoursWorked($record, $employee);

        // Determine remarks based on time in/out
        if (!$record->remarks) {
            // actualTimeIn is now a full datetime
            $actualIn = Carbon::parse($record->actualTimeIn);
            $isLate = $actualIn->greaterThan($shiftStart);
            $isUndertime = $now->lessThan($shiftEnd);
            
            if ($isLate && $isUndertime) {
                // Both late and undertime - prioritize showing both or choose one
                $record->remarks = AttendanceRecord::REMARKS_LATE; // Late takes priority
            } elseif ($isLate) {
                $record->remarks = AttendanceRecord::REMARKS_LATE;
            } elseif ($isUndertime) {
                $record->remarks = AttendanceRecord::REMARKS_UNDERTIME;
            }
        }

        // Handle image upload if provided
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('attendance', 'public');
            $record->image = $path;
        }

        $record->save();

        return back()->with('success', 'Time-Out recorded successfully at ' . $now->format('h:i A'));
    }

    /**
     * Calculate hours worked based on actual times, capped at employee's totalWorkHours.
     */
    private function calculateHoursWorked(AttendanceRecord $record, Employee $employee): float
    {
        if (!$record->actualTimeIn || !$record->actualTimeOut) {
            return 0;
        }

        // All time fields are now full datetime values
        $shiftIn = Carbon::parse($record->shiftTimeIn);
        $shiftOut = Carbon::parse($record->shiftTimeOut);
        $timeIn = Carbon::parse($record->actualTimeIn);
        $timeOut = Carbon::parse($record->actualTimeOut);

        // Use the later of actualTimeIn or shiftTimeIn as effective start
        $effectiveStart = $timeIn->greaterThan($shiftIn) ? $timeIn : $shiftIn;

        // Use the earlier of actualTimeOut or shiftTimeOut as effective end
        $effectiveEnd = $timeOut->lessThan($shiftOut) ? $timeOut : $shiftOut;

        // Handle case where effective end is before effective start (edge case)
        if ($effectiveEnd->lessThanOrEqualTo($effectiveStart)) {
            return 0;
        }

        $workedMinutes = $effectiveStart->diffInMinutes($effectiveEnd);
        $hoursWorked = round($workedMinutes / 60, 2);

        // Cap at employee's work schedule totalWorkHours
        $maxHours = $employee->workSchedule ? (float) $employee->workSchedule->totalWorkHours : 8.0;

        return min($hoursWorked, $maxHours);
    }

    /**
     * Display the attendance history/index page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $employee = Employee::where('userID', $user->id)->first();

        if (!$employee) {
            $records = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 15);
            return view('attendance.index', compact('employee', 'records'));
        }

        $query = AttendanceRecord::where('employee_id', $employee->employeeID);

        // Filter by date range if provided
        if ($request->filled('from_date')) {
            $query->whereDate('workDate', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->whereDate('workDate', '<=', $request->to_date);
        }

        $records = $query->orderBy('workDate', 'desc')->paginate(15);

        return view('attendance.index', compact('employee', 'records'));
    }

    /**
     * Display a specific attendance record.
     */
    public function show(AttendanceRecord $attendanceRecord)
    {
        $user = Auth::user();
        $employee = Employee::where('userID', $user->id)->first();

        // Ensure user can only view their own records
        if (!$employee || $attendanceRecord->employee_id !== $employee->employeeID) {
            abort(403, 'Unauthorized access.');
        }

        return view('attendance.show', compact('attendanceRecord'));
    }

    /**
     * Display the Employee Attendance Calendar (Admin/HR view).
     */
    public function calendar(Request $request)
    {
        // Get search term
        $search = $request->input('search');

        // Get all employees with their work schedules
        $employeesQuery = Employee::with(['user', 'workSchedule']);
        
        // Apply search filter
        if ($search) {
            $employeesQuery->whereHas('user', function ($query) use ($search) {
                $query->where('firstName', 'like', "%{$search}%")
                    ->orWhere('lastName', 'like', "%{$search}%")
                    ->orWhere('middleName', 'like', "%{$search}%");
            });
        }
        
        $employees = $employeesQuery->get();

        // Get selected month/year (default to current)
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);
        $viewMode = $request->input('view', 'calendar'); // 'calendar' or 'list'
        $selectedEmployeeId = $request->input('employee_id');

        // Create date range for the month
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        // Get all attendance records for the month
        $query = AttendanceRecord::with(['employee.user', 'employee.workSchedule'])
            ->whereBetween('workDate', [$startDate, $endDate]);

        if ($selectedEmployeeId) {
            $query->where('employee_id', $selectedEmployeeId);
        }

        // Apply search filter to attendance records
        if ($search) {
            $query->whereHas('employee.user', function ($q) use ($search) {
                $q->where('firstName', 'like', "%{$search}%")
                    ->orWhere('lastName', 'like', "%{$search}%")
                    ->orWhere('middleName', 'like', "%{$search}%");
            });
        }

        $attendanceRecords = $query->orderBy('workDate')->orderBy('employee_id')->get();

        // Group records by date and employee for calendar view
        $calendarData = [];
        foreach ($attendanceRecords as $record) {
            $dateKey = $record->workDate->format('Y-m-d');
            if (!isset($calendarData[$dateKey])) {
                $calendarData[$dateKey] = [];
            }
            $calendarData[$dateKey][$record->employee_id] = $record;
        }

        // For list view, paginate the records
        if ($viewMode === 'list') {
            $listQuery = AttendanceRecord::with(['employee.user', 'employee.workSchedule'])
                ->whereBetween('workDate', [$startDate, $endDate]);
            
            if ($selectedEmployeeId) {
                $listQuery->where('employee_id', $selectedEmployeeId);
            }

            // Apply search filter to list view
            if ($search) {
                $listQuery->whereHas('employee.user', function ($q) use ($search) {
                    $q->where('firstName', 'like', "%{$search}%")
                        ->orWhere('lastName', 'like', "%{$search}%")
                        ->orWhere('middleName', 'like', "%{$search}%");
                });
            }

            $listRecords = $listQuery->orderBy('workDate', 'desc')
                ->orderBy('employee_id')
                ->paginate(20)
                ->appends($request->query());
        } else {
            $listRecords = null;
        }

        // Generate calendar days
        $calendarDays = [];
        $firstDayOfMonth = $startDate->copy();
        $lastDayOfMonth = $endDate->copy();
        
        // Get the day of week for first day (0 = Sunday, 6 = Saturday)
        $startPadding = $firstDayOfMonth->dayOfWeek;
        
        // Add padding for days before the 1st
        for ($i = 0; $i < $startPadding; $i++) {
            $calendarDays[] = null;
        }
        
        // Add all days of the month
        $currentDay = $firstDayOfMonth->copy();
        while ($currentDay->lte($lastDayOfMonth)) {
            $calendarDays[] = $currentDay->copy();
            $currentDay->addDay();
        }

        return view('attendance.calendar', compact(
            'employees',
            'calendarData',
            'calendarDays',
            'startDate',
            'endDate',
            'month',
            'year',
            'viewMode',
            'selectedEmployeeId',
            'listRecords'
        ));
    }
}
