<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('work-schedules.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $workSchedule->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Schedule Details Card --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <div class="h-16 w-16 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <svg class="w-10 h-10 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-3">
                                    <h3 class="text-2xl font-bold text-gray-900">{{ $workSchedule->name }}</h3>
                                    @if($workSchedule->isDefault)
                                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Default</span>
                                    @endif
                                </div>
                                <p class="text-gray-500">Work Schedule</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if(!$workSchedule->isDefault)
                                <form action="{{ route('work-schedules.set-default', $workSchedule) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-green-700 transition">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Set as Default
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('work-schedules.edit', $workSchedule) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 transition">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </a>
                        </div>
                    </div>
                </div>

                <div class="p-6 space-y-6">
                    {{-- Time Details --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Work Hours --}}
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-10 w-10 bg-green-100 rounded-lg flex items-center justify-center">
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
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="h-10 w-10 bg-yellow-100 rounded-lg flex items-center justify-center">
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
                                <span class="px-4 py-2 text-sm font-medium rounded-lg {{ in_array($abbr, $workingDays) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400' }}">
                                    {{ $full }}
                                </span>
                            @endforeach
                        </div>
                        <p class="mt-2 text-sm text-gray-500">{{ count($workingDays) }} working days per week</p>
                    </div>
                </div>
            </div>

            {{-- Assigned Employees --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-900">Assigned Employees</h3>
                        <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $workSchedule->employees->count() }} employees
                        </span>
                    </div>
                </div>

                @if($workSchedule->employees->count() > 0)
                    <div class="divide-y divide-gray-200">
                        @foreach($workSchedule->employees as $employee)
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
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
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $employee->user->fullName ?? 'Unknown' }}
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                {{ $employee->jobTitle?->value ?? 'No Position' }} • {{ $employee->department?->value ?? 'No Department' }}
                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ route('employees.show', $employee) }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                        View
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No employees assigned</h3>
                        <p class="mt-1 text-sm text-gray-500">This work schedule doesn't have any employees assigned yet.</p>
                    </div>
                @endif
            </div>

            {{-- Back Button --}}
            <div class="mt-6">
                <a href="{{ route('work-schedules.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
