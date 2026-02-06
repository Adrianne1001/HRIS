<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Daily Time Record') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Flash Messages --}}
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if(!$employee)
                {{-- No Employee Record Message --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No Employee Record Found</h3>
                        <p class="mt-2 text-gray-500">Your account is not linked to an employee record. Please contact HR to set up your employee profile.</p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                Return to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Time-In/Out Card --}}
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            {{-- Current Date & Time --}}
                            <div class="text-center mb-8">
                                <p class="text-gray-500 text-sm uppercase tracking-wider">{{ $today->format('l') }}</p>
                                <p class="text-3xl font-bold text-gray-900" id="current-date">{{ $today->format('F d, Y') }}</p>
                                <p class="text-5xl font-bold text-indigo-600 mt-2" id="current-time">--:--:--</p>
                            </div>

                            {{-- Employee Info --}}
                            <div class="text-center mb-8 pb-6 border-b border-gray-200">
                                <div class="flex items-center justify-center mb-4">
                                    @if($employee->profilePic)
                                        <img src="{{ Storage::url($employee->profilePic) }}" alt="Profile" class="h-20 w-20 rounded-full object-cover border-4 border-indigo-100">
                                    @else
                                        <div class="h-20 w-20 rounded-full bg-indigo-100 flex items-center justify-center">
                                            <span class="text-2xl font-bold text-indigo-600">{{ substr($employee->user->firstName, 0, 1) }}{{ substr($employee->user->lastName, 0, 1) }}</span>
                                        </div>
                                    @endif
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900">{{ $employee->user->fullName }}</h3>
                                <p class="text-gray-500">{{ $employee->jobTitle?->value ?? 'No Position' }} • {{ $employee->department?->value ?? 'No Department' }}</p>
                            </div>

                            {{-- Work Schedule Info --}}
                            @if($employee->workSchedule)
                                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Today's Schedule</h4>
                                    <div class="grid grid-cols-3 gap-4 text-center">
                                        <div>
                                            <p class="text-xs text-gray-500">Shift Start</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($employee->workSchedule->startTime)->format('h:i A') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Shift End</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ \Carbon\Carbon::parse($employee->workSchedule->endTime)->format('h:i A') }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-500">Total Shift Hours</p>
                                            <p class="text-lg font-semibold text-gray-900">{{ $employee->workSchedule->formattedTotalHours }}</p>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                                    <p class="text-yellow-800 text-center">
                                        <svg class="inline-block w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                        </svg>
                                        No work schedule assigned. Please contact HR.
                                    </p>
                                </div>
                            @endif

                            {{-- Today's Status --}}
                            @if($todayRecord)
                                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-6">
                                    <h4 class="text-sm font-medium text-indigo-800 uppercase tracking-wider mb-3">Today's Record</h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <p class="text-xs text-indigo-600">Time In</p>
                                            <p class="text-xl font-bold {{ $todayRecord->actualTimeIn ? 'text-green-600' : 'text-gray-400' }}">
                                                {{ $todayRecord->actualTimeIn ? \Carbon\Carbon::parse($todayRecord->actualTimeIn)->format('h:i A') : '--:--' }}
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-indigo-600">Time Out</p>
                                            <p class="text-xl font-bold {{ $todayRecord->actualTimeOut ? 'text-green-600' : 'text-gray-400' }}">
                                                {{ $todayRecord->actualTimeOut ? \Carbon\Carbon::parse($todayRecord->actualTimeOut)->format('h:i A') : '--:--' }}
                                            </p>
                                        </div>
                                    </div>
                                    @if($todayRecord->remarks)
                                        <div class="mt-3 text-center">
                                            <span class="px-3 py-1 text-xs font-semibold rounded-full 
                                                {{ $todayRecord->remarks === 'Late' ? 'bg-red-100 text-red-800' : 
                                                   ($todayRecord->remarks === 'Undertime' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                {{ $todayRecord->remarks }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            @elseif($activeRecord)
                                {{-- Graveyard shift from yesterday still in progress --}}
                                <div class="bg-purple-50 border border-purple-200 rounded-lg p-4 mb-6">
                                    <h4 class="text-sm font-medium text-purple-800 uppercase tracking-wider mb-3">
                                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        Active Overnight Shift (Started {{ \Carbon\Carbon::parse($activeRecord->workDate)->format('M d') }})
                                    </h4>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="text-center">
                                            <p class="text-xs text-purple-600">Time In</p>
                                            <p class="text-xl font-bold text-green-600">
                                                {{ \Carbon\Carbon::parse($activeRecord->actualTimeIn)->format('h:i A') }}
                                            </p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-xs text-purple-600">Time Out</p>
                                            <p class="text-xl font-bold text-gray-400">--:--</p>
                                        </div>
                                    </div>
                                    <div class="mt-3 text-center">
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Shift in progress - Time out to complete
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- Time-In / Time-Out Buttons --}}
                            @php
                                // Determine if there's an active shift (either today's or yesterday's overnight)
                                $canTimeIn = $employee->workSchedule && (!$todayRecord || !$todayRecord->actualTimeIn) && !$activeRecord;
                                $canTimeOut = ($todayRecord && $todayRecord->actualTimeIn && !$todayRecord->actualTimeOut) || $activeRecord;
                            @endphp
                            <div class="grid grid-cols-2 gap-4">
                                {{-- Time-In Button --}}
                                <form action="{{ route('attendance.time-in') }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                        @if(!$canTimeIn) disabled @endif
                                        class="w-full py-6 px-4 rounded-xl font-bold text-xl transition-all duration-200
                                            {{ !$canTimeIn 
                                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                                : 'bg-green-500 hover:bg-green-600 text-white shadow-lg hover:shadow-xl transform hover:-translate-y-1' }}">
                                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                                        </svg>
                                        TIME IN
                                    </button>
                                </form>

                                {{-- Time-Out Button --}}
                                <form action="{{ route('attendance.time-out') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        @if(!$canTimeOut) disabled @endif
                                        class="w-full py-6 px-4 rounded-xl font-bold text-xl transition-all duration-200
                                            {{ !$canTimeOut 
                                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed' 
                                                : 'bg-red-500 hover:bg-red-600 text-white shadow-lg hover:shadow-xl transform hover:-translate-y-1' }}">
                                        <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        TIME OUT
                                    </button>
                                </form>
                            </div>

                            {{-- Status Messages --}}
                            <div class="mt-4 text-center">
                                @if(!$employee->workSchedule)
                                    <p class="text-sm text-gray-500">Cannot record attendance without a work schedule.</p>
                                @elseif($activeRecord)
                                    <p class="text-sm text-purple-600 font-medium">
                                        <svg class="inline-block w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                        </svg>
                                        You're on an overnight shift. Click <strong>TIME OUT</strong> to complete your shift.
                                    </p>
                                @elseif(!$todayRecord || !$todayRecord->actualTimeIn)
                                    <p class="text-sm text-gray-500">Click <strong>TIME IN</strong> to start your work day.</p>
                                @elseif($todayRecord && $todayRecord->actualTimeIn && !$todayRecord->actualTimeOut)
                                    <p class="text-sm text-gray-500">You're currently clocked in. Click <strong>TIME OUT</strong> when you're done.</p>
                                @else
                                    <p class="text-sm text-green-600 font-medium">✓ You have completed your attendance for today.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Recent Records --}}
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Recent Records</h3>
                                <a href="{{ route('attendance.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">View All</a>
                            </div>

                            @if($recentRecords->count() > 0)
                                <div class="space-y-3">
                                    @foreach($recentRecords as $record)
                                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-colors">
                                            <div class="flex justify-between items-start mb-2">
                                                <div>
                                                    <p class="font-medium text-gray-900">{{ \Carbon\Carbon::parse($record->workDate)->format('M d, Y') }}</p>
                                                    <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($record->workDate)->format('l') }}</p>
                                                </div>
                                                @if($record->remarks)
                                                    <span class="px-2 py-0.5 text-xs font-semibold rounded-full 
                                                        {{ $record->remarks === 'Late' ? 'bg-red-100 text-red-800' : 
                                                           ($record->remarks === 'Undertime' ? 'bg-orange-100 text-orange-800' :
                                                           (in_array($record->remarks, ['Vacation Leave', 'Sick Leave']) ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800')) }}">
                                                        {{ $record->remarks }}
                                                    </span>
                                                @endif
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 text-sm">
                                                <div>
                                                    <span class="text-gray-500">In:</span>
                                                    <span class="font-medium {{ $record->actualTimeIn ? 'text-gray-900' : 'text-gray-400' }}">
                                                        {{ $record->actualTimeIn ? \Carbon\Carbon::parse($record->actualTimeIn)->format('h:i A') : '--:--' }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-500">Out:</span>
                                                    <span class="font-medium {{ $record->actualTimeOut ? 'text-gray-900' : 'text-gray-400' }}">
                                                        {{ $record->actualTimeOut ? \Carbon\Carbon::parse($record->actualTimeOut)->format('h:i A') : '--:--' }}
                                                    </span>
                                                </div>
                                            </div>
                                            @if($record->hoursWorked > 0)
                                                <div class="mt-2 text-xs text-gray-500">
                                                    Hours: <span class="font-medium text-gray-700">{{ $record->formattedHoursWorked }}</span>
                                                    @if($record->advanceOTHours > 0 || $record->afterShiftOTHours > 0)
                                                        | OT: <span class="font-medium text-indigo-600">{{ $record->formattedTotalOTHours }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <p class="mt-2 text-sm text-gray-500">No attendance records yet.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('en-US', { 
                hour: '2-digit', 
                minute: '2-digit', 
                second: '2-digit',
                hour12: true 
            });
            const timeEl = document.getElementById('current-time');
            if (timeEl) {
                timeEl.textContent = timeString;
            }
        }
        
        // Update immediately and then every second
        updateTime();
        setInterval(updateTime, 1000);
    </script>
    @endpush
</x-app-layout>
