<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('work-schedules.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ $workSchedule->name }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            {{-- Schedule Details Card --}}
            <div class="card">
                <div class="card-header">
                    <div class="header-row-between">
                        <div class="header-row">
                            <div class="icon-box-lg bg-indigo-100">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $workSchedule->name }}</h3>
                                    @if($workSchedule->isDefault)
                                        <span class="badge-default">Default</span>
                                    @endif
                                </div>
                                <p class="text-gray-500">Work Schedule</p>
                            </div>
                        </div>
                        <div class="table-actions">
                            @if(!$workSchedule->isDefault)
                                <form action="{{ route('work-schedules.set-default', $workSchedule) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="btn-success">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Set as Default
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('work-schedules.edit', $workSchedule) }}" class="btn-primary">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Time Details --}}
                    <div class="form-grid-2">
                        {{-- Work Hours --}}
                        <div class="info-box">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="icon-box-sm bg-green-100">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900">Work Hours</h4>
                            </div>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Start Time</span>
                                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($workSchedule->startTime)->format('h:i A') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">End Time</span>
                                    <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($workSchedule->endTime)->format('h:i A') }}</span>
                                </div>
                                <div class="flex justify-between pt-2 border-t border-gray-200">
                                    <span class="text-gray-600">Total Hours</span>
                                    <span class="font-medium text-gray-900">{{ $workSchedule->formattedTotalHours }}</span>
                                </div>
                            </div>
                        </div>

                        {{-- Break Time --}}
                        <div class="info-box">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="icon-box-sm bg-yellow-100">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h4 class="font-semibold text-gray-900">Break Time</h4>
                            </div>
                            @if($workSchedule->startBreakTime && $workSchedule->endBreakTime)
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Start</span>
                                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($workSchedule->startBreakTime)->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">End</span>
                                        <span class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($workSchedule->endBreakTime)->format('h:i A') }}</span>
                                    </div>
                                    <div class="flex justify-between pt-2 border-t border-gray-200">
                                        <span class="text-gray-600">Duration</span>
                                        @php
                                            $breakDuration = \Carbon\Carbon::parse($workSchedule->startBreakTime)->diffInMinutes(\Carbon\Carbon::parse($workSchedule->endBreakTime));
                                        @endphp
                                        <span class="font-medium text-gray-900">{{ $breakDuration }} minutes</span>
                                    </div>
                                </div>
                            @else
                                <p class="text-gray-500 italic">No break time configured</p>
                            @endif
                        </div>
                    </div>

                    {{-- Working Days --}}
                    <div>
                        <h4 class="font-semibold text-gray-900 mb-3">Working Days</h4>
                        <div class="flex flex-wrap gap-2">
                            @php
                                $allDays = [
                                    'Mon' => 'Monday',
                                    'Tue' => 'Tuesday',
                                    'Wed' => 'Wednesday',
                                    'Thu' => 'Thursday',
                                    'Fri' => 'Friday',
                                    'Sat' => 'Saturday',
                                    'Sun' => 'Sunday',
                                ];
                                $workingDays = $workSchedule->workingDaysArray;
                            @endphp
                            @foreach($allDays as $abbr => $full)
                                <span class="{{ in_array($abbr, $workingDays) ? 'day-pill-lg-active' : 'day-pill-lg-inactive' }}">
                                    {{ $full }}
                                </span>
                            @endforeach
                        </div>
                        <p class="mt-2 text-sm text-gray-500">{{ count($workingDays) }} working days per week</p>
                    </div>
                </div>
            </div>

            {{-- Assigned Employees --}}
            <div class="mt-6 card">
                <div class="card-header">
                    <div class="header-row-between">
                        <h3 class="text-lg font-medium text-gray-900">Assigned Employees</h3>
                        <span class="badge-count">
                            {{ $workSchedule->employees->count() }} employees
                        </span>
                    </div>
                </div>

                @if($workSchedule->employees->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($workSchedule->employees as $employee)
                            <div class="table-row">
                                <div class="header-row-between">
                                    <div class="table-avatar-cell">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($employee->profilePic)
                                                <img class="h-10 w-10 rounded-full object-cover" src="{{ $employee->profilePic }}" alt="{{ $employee->user->fullName ?? 'Employee' }}">
                                            @else
                                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                    <span class="text-sm font-medium text-gray-600">
                                                        {{ strtoupper(substr($employee->user->firstName ?? 'E', 0, 1)) }}
                                                    </span>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <div class="table-avatar-name">
                                                {{ $employee->user->fullName ?? 'Unknown' }}
                                            </div>
                                            <div class="table-avatar-sub">
                                                {{ $employee->jobTitle?->value ?? 'No Position' }} • {{ $employee->department?->value ?? 'No Department' }}
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('employees.show', $employee) }}" class="action-edit">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="empty-state">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="empty-state-title">No employees assigned</h3>
                        <p class="empty-state-text">This work schedule doesn't have any employees assigned yet.</p>
                    </div>
                @endif
            </div>

            {{-- Back Button --}}
            <div class="mt-6">
                <a href="{{ route('work-schedules.index') }}" class="btn-ghost">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
