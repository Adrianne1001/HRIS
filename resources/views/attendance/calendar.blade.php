<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Employee Attendance Calendar') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-[1600px] mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Filters Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('attendance.calendar') }}" class="flex flex-wrap items-end gap-4">
                        {{-- Search Bar --}}
                        <div class="min-w-[280px]">
                            <x-input-label for="search" :value="__('Search Employee')" />
                            <div class="relative mt-1">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <x-text-input id="search" name="search" type="text" class="block w-full pl-10" 
                                    placeholder="Search by name..." 
                                    :value="request('search')" />
                            </div>
                        </div>

                        {{-- Month/Year Selector --}}
                        <div>
                            <x-input-label for="month" :value="__('Month')" />
                            <select id="month" name="month" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected($month == $m)>
                                        {{ \Carbon\Carbon::create(null, $m)->format('F') }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <x-input-label for="year" :value="__('Year')" />
                            <select id="year" name="year" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                @foreach(range(date('Y') - 2, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Employee Filter --}}
                        <div class="min-w-[250px]">
                            <x-input-label for="employee_id" :value="__('Employee')" />
                            <select id="employee_id" name="employee_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">All Employees</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->employeeID }}" @selected($selectedEmployeeId == $emp->employeeID)>
                                        {{ $emp->user->fullName }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- View Mode Toggle --}}
                        <div>
                            <x-input-label :value="__('View Mode')" />
                            <div class="mt-1 inline-flex rounded-md shadow-sm">
                                <button type="submit" name="view" value="calendar"
                                    class="px-4 py-2 text-sm font-medium rounded-l-md border {{ $viewMode === 'calendar' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    Calendar
                                </button>
                                <button type="submit" name="view" value="list"
                                    class="px-4 py-2 text-sm font-medium rounded-r-md border-t border-r border-b {{ $viewMode === 'list' ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-50' }}">
                                    <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    List
                                </button>
                            </div>
                        </div>

                        <div>
                            <x-primary-button type="submit">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Legend --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-4">
                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <span class="font-medium text-gray-700">Legend:</span>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-green-100 border border-green-300"></span>
                            Present
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-red-100 border border-red-300"></span>
                            Late
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-orange-100 border border-orange-300"></span>
                            Undertime
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-blue-100 border border-blue-300"></span>
                            Leave
                        </span>
                        <span class="flex items-center gap-2">
                            <span class="w-4 h-4 rounded bg-white border border-gray-200"></span>
                            No Record
                        </span>
                    </div>
                </div>
            </div>

            @if($viewMode === 'calendar')
                {{-- Calendar View - One calendar per employee --}}
                @php
                    $displayEmployees = $selectedEmployeeId 
                        ? $employees->where('employeeID', $selectedEmployeeId) 
                        : $employees;
                @endphp

                <div class="space-y-8">
                    @foreach($displayEmployees as $employee)
                        @php
                            $workingDays = [];
                            if ($employee->workSchedule) {
                                $workingDays = $employee->workSchedule->workingDaysArrayAttribute ?? [];
                            }
                        @endphp
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                            <div class="p-6">
                                {{-- Employee Header --}}
                                <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-200">
                                    <div class="flex items-center gap-4">
                                        @if($employee->profilePic)
                                            <img class="h-14 w-14 rounded-full object-cover border-2 border-indigo-100" src="{{ Storage::url($employee->profilePic) }}" alt="">
                                        @else
                                            <div class="h-14 w-14 rounded-full bg-indigo-100 flex items-center justify-center border-2 border-indigo-200">
                                                <span class="text-lg font-bold text-indigo-600">
                                                    {{ substr($employee->user->firstName, 0, 1) }}{{ substr($employee->user->lastName, 0, 1) }}
                                                </span>
                                            </div>
                                        @endif
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">{{ $employee->user->fullName }}</h3>
                                            <p class="text-sm text-gray-500">
                                                {{ $employee->jobTitle?->value ?? 'No Position' }} • {{ $employee->department?->value ?? 'No Department' }}
                                            </p>
                                            @if($employee->workSchedule)
                                                <p class="text-xs text-gray-400 mt-1">
                                                    Schedule: {{ \Carbon\Carbon::parse($employee->workSchedule->startTime)->format('g:i A') }} - {{ \Carbon\Carbon::parse($employee->workSchedule->endTime)->format('g:i A') }}
                                                    @if($employee->workSchedule->workingDays)
                                                        • {{ $employee->workSchedule->workingDays }}
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    {{-- Month Navigation --}}
                                    <div class="flex items-center gap-2">
                                        @php
                                            $prevMonth = $month - 1;
                                            $prevYear = $year;
                                            if ($prevMonth < 1) {
                                                $prevMonth = 12;
                                                $prevYear--;
                                            }
                                            $nextMonth = $month + 1;
                                            $nextYear = $year;
                                            if ($nextMonth > 12) {
                                                $nextMonth = 1;
                                                $nextYear++;
                                            }
                                        @endphp
                                        <a href="{{ route('attendance.calendar', ['month' => $prevMonth, 'year' => $prevYear, 'view' => $viewMode, 'employee_id' => $selectedEmployeeId]) }}"
                                           class="p-2 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                            </svg>
                                        </a>
                                        <h4 class="text-lg font-semibold text-gray-900 min-w-[160px] text-center">
                                            {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
                                        </h4>
                                        <a href="{{ route('attendance.calendar', ['month' => $nextMonth, 'year' => $nextYear, 'view' => $viewMode, 'employee_id' => $selectedEmployeeId]) }}"
                                           class="p-2 rounded-md bg-gray-100 hover:bg-gray-200 text-gray-700">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </a>
                                    </div>
                                </div>

                                {{-- Calendar Grid --}}
                                <div class="grid grid-cols-7 gap-2">
                                    {{-- Day Headers --}}
                                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $dayName)
                                        @php
                                            $isWorkDay = count($workingDays) === 0 || in_array($dayName, $workingDays);
                                        @endphp
                                        <div class="text-center py-3 font-semibold text-sm rounded-t-lg {{ $isWorkDay ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                            {{ substr($dayName, 0, 3) }}
                                        </div>
                                    @endforeach

                                    {{-- Calendar Days --}}
                                    @foreach($calendarDays as $day)
                                        @if($day === null)
                                            <div class="min-h-[100px] bg-gray-50 rounded-lg"></div>
                                        @else
                                            @php
                                                $dateKey = $day->format('Y-m-d');
                                                $record = $calendarData[$dateKey][$employee->employeeID] ?? null;
                                                $isToday = $day->isToday();
                                                $isPast = $day->isPast() && !$isToday;
                                                $isFuture = $day->isFuture();
                                                $dayOfWeek = $day->format('l');
                                                $isRestDay = count($workingDays) > 0 && !in_array($dayOfWeek, $workingDays);
                                                
                                                // Determine cell background
                                                $cellBg = 'bg-white';
                                                $cellBorder = 'border-gray-200';
                                                if ($isToday) {
                                                    $cellBorder = 'border-indigo-400 border-2';
                                                }
                                                // Remove special coloring for Rest Day and No Time In/Out
                                                if ($record) {
                                                    if ($record->remarks === 'Late') {
                                                        $cellBg = 'bg-red-50';
                                                        $cellBorder = $isToday ? 'border-indigo-400 border-2' : 'border-red-200';
                                                    } elseif ($record->remarks === 'Undertime') {
                                                        $cellBg = 'bg-orange-50';
                                                        $cellBorder = $isToday ? 'border-indigo-400 border-2' : 'border-orange-200';
                                                    } elseif (in_array($record->remarks, ['Vacation Leave', 'Sick Leave'])) {
                                                        $cellBg = 'bg-blue-50';
                                                        $cellBorder = $isToday ? 'border-indigo-400 border-2' : 'border-blue-200';
                                                    } else {
                                                        $cellBg = 'bg-green-50';
                                                        $cellBorder = $isToday ? 'border-indigo-400 border-2' : 'border-green-200';
                                                    }
                                                } elseif ($isFuture) {
                                                    $cellBg = 'bg-gray-50';
                                                }
                                            @endphp
                                            
                                            <div x-data="{ showTooltip: false }" 
                                                 @mouseenter="showTooltip = true" 
                                                 @mouseleave="showTooltip = false"
                                                 class="min-h-[100px] {{ $cellBg }} border {{ $cellBorder }} rounded-lg p-2 relative cursor-pointer hover:shadow-md transition-shadow">
                                                {{-- Day Number --}}
                                                <div class="flex items-center justify-between mb-1">
                                                    <span class="text-sm font-semibold {{ $isToday ? 'bg-indigo-600 text-white px-2 py-0.5 rounded-full' : ($isRestDay ? 'text-gray-400' : 'text-gray-700') }}">
                                                        {{ $day->day }}
                                                    </span>
                                                    @if($isRestDay)
                                                        <span class="text-xs text-gray-400 font-medium">REST</span>
                                                    @endif
                                                </div>
                                                
                                                {{-- Content --}}
                                                @if($isRestDay)
                                                    <div class="text-xs text-gray-400 text-center mt-4">Rest Day</div>
                                                @elseif($record)
                                                    <div class="space-y-1 text-xs">
                                                        <div class="flex items-center gap-1">
                                                            <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14"/>
                                                            </svg>
                                                            <span class="font-medium {{ $record->actualTimeIn ? 'text-gray-700' : 'text-gray-400' }}">
                                                                {{ $record->actualTimeIn ? \Carbon\Carbon::parse($record->actualTimeIn)->format('g:i A') : '--:--' }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center gap-1">
                                                            <svg class="w-3 h-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"/>
                                                            </svg>
                                                            <span class="font-medium {{ $record->actualTimeOut ? 'text-gray-700' : 'text-gray-400' }}">
                                                                {{ $record->actualTimeOut ? \Carbon\Carbon::parse($record->actualTimeOut)->format('g:i A') : '--:--' }}
                                                            </span>
                                                        </div>
                                                        @if($record->remarks)
                                                            <div class="mt-1">
                                                                <span class="inline-block px-1.5 py-0.5 rounded text-xs font-medium
                                                                    @if($record->remarks === 'Late') bg-red-200 text-red-800
                                                                    @elseif($record->remarks === 'Undertime') bg-orange-200 text-orange-800
                                                                    @elseif(in_array($record->remarks, ['Vacation Leave', 'Sick Leave'])) bg-blue-200 text-blue-800
                                                                    @else bg-yellow-200 text-yellow-800
                                                                    @endif">
                                                                    {{ $record->remarks }}
                                                                </span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($isPast && !$isFuture)
                                                    <div class="text-xs text-gray-400 text-center mt-4">No Record</div>
                                                @endif

                                                {{-- Tooltip --}}
                                                @if($record || $isRestDay)
                                                <div x-show="showTooltip" 
                                                     x-transition:enter="transition ease-out duration-200"
                                                     x-transition:enter-start="opacity-0 transform scale-95"
                                                     x-transition:enter-end="opacity-100 transform scale-100"
                                                     x-transition:leave="transition ease-in duration-150"
                                                     x-transition:leave-start="opacity-100 transform scale-100"
                                                     x-transition:leave-end="opacity-0 transform scale-95"
                                                     class="absolute z-50 left-1/2 -translate-x-1/2 top-full mt-2 w-72 bg-gray-900 text-white text-xs rounded-xl shadow-2xl p-4"
                                                     style="display: none;"
                                                     @click.outside="showTooltip = false">
                                                    <div class="font-bold text-sm mb-3 pb-2 border-b border-gray-700">
                                                        {{ $day->format('l, F d, Y') }}
                                                    </div>
                                                    @if($isRestDay)
                                                        <div class="flex items-center gap-2 text-gray-300">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                                            </svg>
                                                            <span>Rest Day - No work scheduled</span>
                                                        </div>
                                                    @elseif($record)
                                                        <div class="space-y-2">
                                                            <div class="flex justify-between items-center">
                                                                <span class="text-gray-400">Shift Schedule:</span>
                                                                <span class="font-medium">{{ \Carbon\Carbon::parse($record->shiftTimeIn)->format('g:i A') }} - {{ \Carbon\Carbon::parse($record->shiftTimeOut)->format('g:i A') }}</span>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <span class="text-gray-400">Time In:</span>
                                                                <span class="font-medium {{ $record->actualTimeIn ? 'text-green-400' : 'text-gray-500' }}">
                                                                    {{ $record->actualTimeIn ? \Carbon\Carbon::parse($record->actualTimeIn)->format('M d, g:i A') : 'Not recorded' }}
                                                                </span>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <span class="text-gray-400">Time Out:</span>
                                                                <span class="font-medium {{ $record->actualTimeOut ? 'text-red-400' : 'text-gray-500' }}">
                                                                    {{ $record->actualTimeOut ? \Carbon\Carbon::parse($record->actualTimeOut)->format('M d, g:i A') : 'Not recorded' }}
                                                                </span>
                                                            </div>
                                                            <div class="flex justify-between items-center">
                                                                <span class="text-gray-400">Hours Worked:</span>
                                                                <span class="font-medium">{{ $record->hoursWorked > 0 ? $record->formattedHoursWorked : '--' }}</span>
                                                            </div>
                                                            @if($record->advanceOTHours > 0)
                                                                <div class="flex justify-between items-center">
                                                                    <span class="text-gray-400">Advance OT:</span>
                                                                    <span class="font-medium text-blue-400">{{ $record->formattedAdvanceOTHours }}</span>
                                                                </div>
                                                            @endif
                                                            @if($record->afterShiftOTHours > 0)
                                                                <div class="flex justify-between items-center">
                                                                    <span class="text-gray-400">After-Shift OT:</span>
                                                                    <span class="font-medium text-purple-400">{{ $record->formattedAfterShiftOTHours }}</span>
                                                                </div>
                                                            @endif
                                                            @if($record->remarks)
                                                                <div class="flex justify-between items-center pt-2 border-t border-gray-700">
                                                                    <span class="text-gray-400">Status:</span>
                                                                    <span class="px-2 py-1 rounded-full text-xs font-bold
                                                                        @if($record->remarks === 'Late') bg-red-500 text-white
                                                                        @elseif($record->remarks === 'Undertime') bg-orange-500 text-white
                                                                        @elseif(in_array($record->remarks, ['Vacation Leave', 'Sick Leave'])) bg-blue-500 text-white
                                                                        @else bg-yellow-500 text-gray-900
                                                                        @endif">
                                                                        {{ $record->remarks }}
                                                                    </span>
                                                                </div>
                                                            @else
                                                                <div class="flex justify-between items-center pt-2 border-t border-gray-700">
                                                                    <span class="text-gray-400">Status:</span>
                                                                    <span class="px-2 py-1 rounded-full text-xs font-bold bg-green-500 text-white">Complete</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    {{-- Arrow --}}
                                                    <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-gray-900 transform rotate-45"></div>
                                                </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>

                                {{-- Monthly Summary --}}
                                @php
                                    $employeeRecords = collect($calendarData)->flatMap(function($dayRecords) use ($employee) {
                                        return isset($dayRecords[$employee->employeeID]) ? [$dayRecords[$employee->employeeID]] : [];
                                    });
                                    $totalHours = $employeeRecords->sum('hoursWorked');
                                    $totalAdvanceOT = $employeeRecords->sum('advanceOTHours');
                                    $totalAfterOT = $employeeRecords->sum('afterShiftOTHours');
                                    $lateCount = $employeeRecords->where('remarks', 'Late')->count();
                                    $undertimeCount = $employeeRecords->where('remarks', 'Undertime')->count();
                                    $presentCount = $employeeRecords->count();
                                @endphp
                                <div class="mt-6 pt-4 border-t border-gray-200">
                                    <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-gray-900">{{ $presentCount }}</div>
                                            <div class="text-xs text-gray-500">Days Present</div>
                                        </div>
                                        <div class="bg-indigo-50 rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-indigo-600">{{ number_format($totalHours, 1) }}h</div>
                                            <div class="text-xs text-gray-500">Total Hours</div>
                                        </div>
                                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-blue-600">{{ number_format($totalAdvanceOT, 1) }}h</div>
                                            <div class="text-xs text-gray-500">Advance OT</div>
                                        </div>
                                        <div class="bg-purple-50 rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-purple-600">{{ number_format($totalAfterOT, 1) }}h</div>
                                            <div class="text-xs text-gray-500">After-Shift OT</div>
                                        </div>
                                        <div class="bg-red-50 rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-red-600">{{ $lateCount }}</div>
                                            <div class="text-xs text-gray-500">Times Late</div>
                                        </div>
                                        <div class="bg-orange-50 rounded-lg p-3 text-center">
                                            <div class="text-2xl font-bold text-orange-600">{{ $undertimeCount }}</div>
                                            <div class="text-xs text-gray-500">Undertime</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($displayEmployees->isEmpty())
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <p class="mt-2 text-gray-500">No employees found.</p>
                        </div>
                    </div>
                @endif
            @else
                {{-- List/Table View --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            Attendance Records - {{ \Carbon\Carbon::create($year, $month)->format('F Y') }}
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Employee</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Schedule</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hours</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">OT</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse($listRecords as $record)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    @if($record->employee->profilePic)
                                                        <img class="h-10 w-10 rounded-full object-cover" src="{{ Storage::url($record->employee->profilePic) }}" alt="">
                                                    @else
                                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-indigo-600">
                                                                {{ substr($record->employee->user->firstName, 0, 1) }}{{ substr($record->employee->user->lastName, 0, 1) }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $record->employee->user->fullName }}</div>
                                                        <div class="text-xs text-gray-500">{{ $record->employee->department?->value }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->workDate)->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($record->workDate)->format('l') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($record->shiftTimeIn)->format('g:i A') }} - {{ \Carbon\Carbon::parse($record->shiftTimeOut)->format('g:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->actualTimeIn)
                                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->actualTimeIn)->format('g:i A') }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($record->actualTimeIn)->format('M d') }}</div>
                                                @else
                                                    <span class="text-sm text-gray-400">--</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->actualTimeOut)
                                                    <div class="text-sm text-gray-900">{{ \Carbon\Carbon::parse($record->actualTimeOut)->format('g:i A') }}</div>
                                                    <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($record->actualTimeOut)->format('M d') }}</div>
                                                @else
                                                    <span class="text-sm text-gray-400">--</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                                {{ $record->hoursWorked > 0 ? $record->formattedHoursWorked : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($record->advanceOTHours > 0 || $record->afterShiftOTHours > 0)
                                                    <div class="space-y-1">
                                                        @if($record->advanceOTHours > 0)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                Adv: {{ $record->formattedAdvanceOTHours }}
                                                            </span>
                                                        @endif
                                                        @if($record->afterShiftOTHours > 0)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                                Aft: {{ $record->formattedAfterShiftOTHours }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->remarks)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                                                        @if($record->remarks === 'Late') bg-red-100 text-red-800
                                                        @elseif($record->remarks === 'Undertime') bg-orange-100 text-orange-800
                                                        @elseif(in_array($record->remarks, ['Vacation Leave', 'Sick Leave'])) bg-blue-100 text-blue-800
                                                        @else bg-yellow-100 text-yellow-800
                                                        @endif">
                                                        {{ $record->remarks }}
                                                    </span>
                                                @else
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        Complete
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                                </svg>
                                                <p class="mt-2 text-sm">No attendance records found for this period.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        @if($listRecords && $listRecords->hasPages())
                            <div class="mt-4">
                                {{ $listRecords->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
