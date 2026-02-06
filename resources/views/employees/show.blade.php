<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="{{ route('employees.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ __('Employee Details') }}
                </h2>
            </div>
            
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Profile Header --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center gap-6">
                        {{-- Avatar --}}
                        <div class="flex-shrink-0">
                            @if($employee->profilePic)
                                <img class="h-24 w-24 rounded-full object-cover ring-4 ring-indigo-100" src="{{ asset('storage/' . $employee->profilePic) }}" alt="{{ $employee->user->fullName }}">
                            @else
                                <div class="h-24 w-24 rounded-full bg-indigo-100 flex items-center justify-center ring-4 ring-indigo-50">
                                    <span class="text-indigo-700 font-bold text-2xl">
                                        {{ substr($employee->user->firstName, 0, 1) }}{{ substr($employee->user->lastName, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        {{-- Basic Info --}}
                        <div class="flex-1">
                            <h3 class="text-2xl font-bold text-gray-900">{{ $employee->user->fullName }}</h3>
                            <p class="text-indigo-600 font-medium">{{ $employee->jobTitle->value }}</p>
                            <p class="text-gray-500">{{ $employee->department->value }}</p>
                            
                            <div class="mt-3 flex flex-wrap gap-2">
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $employee->employmentStatus->value === 'Active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $employee->employmentStatus->value }}
                                </span>
                                @php
                                    $typeColors = [
                                        'Regular' => 'bg-blue-100 text-blue-800',
                                        'Probationary' => 'bg-yellow-100 text-yellow-800',
                                        'Contractual' => 'bg-purple-100 text-purple-800',
                                        'Temporary' => 'bg-orange-100 text-orange-800',
                                        'Casual' => 'bg-gray-100 text-gray-800',
                                        'Project-based' => 'bg-teal-100 text-teal-800',
                                    ];
                                @endphp
                                <span class="px-3 py-1 inline-flex text-sm font-semibold rounded-full {{ $typeColors[$employee->employmentType->value] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $employee->employmentType->value }}
                                </span>
                            </div>
                        </div>

                        {{-- Quick Stats --}}
                        <div class="flex-shrink-0 text-right">
                            <p class="text-sm text-gray-500">Employee ID</p>
                            <p class="text-lg font-semibold text-gray-900">EMP-{{ str_pad($employee->employeeID, 5, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-sm text-gray-500 mt-2">Hire Date</p>
                            <p class="text-lg font-semibold text-gray-900">{{ $employee->hireDate->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Contact Information --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Contact Information
                        </h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="mailto:{{ $employee->user->email }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $employee->user->email }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    <a href="tel:{{ $employee->phoneNbr }}" class="text-indigo-600 hover:text-indigo-900">
                                        {{ $employee->phoneNbr }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Address</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->address }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Personal Information
                        </h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Date of Birth</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ $employee->dateOfBirth->format('F d, Y') }}
                                    <span class="text-gray-500">({{ $employee->dateOfBirth->age }} years old)</span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Gender</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->gender->value }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Marital Status</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->maritalStatus->value }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Employment Details --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Employment Details
                        </h3>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->department->value }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Position</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $employee->jobTitle->value }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Basic Monthly Salary</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-semibold">
                                    ₱{{ number_format($employee->basicMonthlySalary, 2) }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Years of Service</dt>
                                <dd class="mt-1 text-sm text-gray-900">
                                    {{ (int) $employee->hireDate->diffInYears(now()) }} year/s, {{ $employee->hireDate->diffInMonths(now()) % 12 }} month/s
                                </dd>
                            </div>
                        </dl>
                    </div>
                </div>

                {{-- Emergency Contact --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Emergency Contact
                        </h3>
                        
                        @if($employee->emergencyContactName || $employee->emergencyContactPhoneNbr)
                            <dl class="space-y-4">
                                @if($employee->emergencyContactName)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact Name</dt>
                                    <dd class="mt-1 text-sm text-gray-900">{{ $employee->emergencyContactName }}</dd>
                                </div>
                                @endif
                                @if($employee->emergencyContactPhoneNbr)
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Contact Phone</dt>
                                    <dd class="mt-1 text-sm text-gray-900">
                                        <a href="tel:{{ $employee->emergencyContactPhoneNbr }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ $employee->emergencyContactPhoneNbr }}
                                        </a>
                                    </dd>
                                </div>
                                @endif
                            </dl>
                        @else
                            <p class="text-sm text-gray-500 italic">No emergency contact information provided.</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Work Schedule --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Work Schedule
                    </h3>
                    
                    @if($employee->workSchedule)
                        <div class="flex flex-col md:flex-row md:items-start gap-6">
                            {{-- Schedule Info --}}
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="h-12 w-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-semibold text-gray-900">{{ $employee->workSchedule->name }}</p>
                                        @if($employee->workSchedule->isDefault)
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Default Schedule</span>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-sm font-medium text-gray-500 mb-1">Work Hours</p>
                                        <p class="text-gray-900 font-semibold">
                                            {{ \Carbon\Carbon::parse($employee->workSchedule->startTime)->format('h:i A') }} - {{ \Carbon\Carbon::parse($employee->workSchedule->endTime)->format('h:i A') }}
                                        </p>
                                    </div>
                                    <div class="bg-gray-50 rounded-lg p-4">
                                        <p class="text-sm font-medium text-gray-500 mb-1">Break Time</p>
                                        @if($employee->workSchedule->startBreakTime && $employee->workSchedule->endBreakTime)
                                            <p class="text-gray-900 font-semibold">
                                                {{ \Carbon\Carbon::parse($employee->workSchedule->startBreakTime)->format('h:i A') }} - {{ \Carbon\Carbon::parse($employee->workSchedule->endBreakTime)->format('h:i A') }}
                                            </p>
                                        @else
                                            <p class="text-gray-500 italic">No break time set</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Working Days --}}
                            <div class="md:w-auto">
                                <p class="text-sm font-medium text-gray-500 mb-2">Working Days</p>
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
                                        $workingDays = $employee->workSchedule->workingDaysArray;
                                    @endphp
                                    @foreach($allDays as $abbr => $full)
                                        <span class="px-3 py-1.5 text-sm font-medium rounded-lg {{ in_array($abbr, $workingDays) ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-400' }}">
                                            {{ $abbr }}
                                        </span>
                                    @endforeach
                                </div>
                                <p class="mt-2 text-sm text-gray-500">{{ count($workingDays) }} days/week</p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-6">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p class="mt-2 text-sm text-gray-500 italic">No work schedule assigned to this employee.</p>
                            <a href="{{ route('employees.edit', $employee) }}" class="mt-3 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-800">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Assign a work schedule
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            {{-- System Information --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                        <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        System Information
                    </h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-sm">
                        <div>
                            <span class="text-gray-500">Created:</span>
                            <span class="text-gray-900 ml-2">
                                {{ $employee->CreatedDateTime ? $employee->CreatedDateTime->format('M d, Y h:i A') : 'N/A' }}
                                @if($employee->createdBy)
                                    <span class="text-gray-500">by {{ $employee->createdBy->fullName }}</span>
                                @endif
                            </span>
                        </div>
                        <div>
                            <span class="text-gray-500">Last Modified:</span>
                            <span class="text-gray-900 ml-2">
                                {{ $employee->LastModifiedDateTime ? $employee->LastModifiedDateTime->format('M d, Y h:i A') : 'N/A' }}
                                @if($employee->lastModifiedBy)
                                    <span class="text-gray-500">by {{ $employee->lastModifiedBy->fullName }}</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to List
                </a>
                
                <div class="flex items-center gap-3">
                    <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Employee
                    </a>
                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-sm text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete Employee
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
