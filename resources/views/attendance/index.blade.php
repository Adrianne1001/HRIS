<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Attendance History') }}
            </h2>          
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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
            {{-- Filter Form --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form action="{{ route('attendance.index') }}" method="GET" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <x-input-label for="from_date" :value="__('From Date')" />
                            <x-text-input id="from_date" name="from_date" type="date" class="mt-1 block" 
                                :value="request('from_date')" />
                        </div>
                        <div>
                            <x-input-label for="to_date" :value="__('To Date')" />
                            <x-text-input id="to_date" name="to_date" type="date" class="mt-1 block" 
                                :value="request('to_date')" />
                        </div>
                        <div class="flex gap-2">
                            <x-primary-button type="submit">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Filter
                            </x-primary-button>
                            @if(request('from_date') || request('to_date'))
                                <a href="{{ route('attendance.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Records Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($records->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Shift Schedule</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time In</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time Out</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Regular Hrs Worked</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Advance OT</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">After-Shift OT</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($records as $record)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($record->workDate)->format('M d, Y') }}</div>
                                                <div class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($record->workDate)->format('l') }}</div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                @php
                                                    $shiftInTime = \Carbon\Carbon::parse($record->shiftTimeIn);
                                                    $shiftOutTime = \Carbon\Carbon::parse($record->shiftTimeOut);
                                                    $isOvernightShift = $shiftOutTime->toDateString() !== $shiftInTime->toDateString();
                                                @endphp
                                                {{ $shiftInTime->format('h:i A') }} - {{ $shiftOutTime->format('h:i A') }}
                                                @if($isOvernightShift)
                                                    <span class="block text-xs text-purple-500">(overnight)</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->actualTimeIn)
                                                    @php
                                                        $actualIn = \Carbon\Carbon::parse($record->actualTimeIn);
                                                        $diffMinutes = $actualIn->diffInMinutes($shiftInTime, false);
                                                    @endphp
                                                    <span class="text-sm font-medium text-gray-900">{{ $actualIn->format('h:i A') }}</span>
                                                    @if($actualIn->toDateString() !== $record->workDate->format('Y-m-d'))
                                                        <span class="block text-xs text-gray-500">{{ $actualIn->format('M d') }}</span>
                                                    @endif
                                                    @if($diffMinutes < 0)
                                                        <span class="block text-xs text-red-600">{{ round(abs($diffMinutes)) }} min late</span>
                                                    @elseif($diffMinutes > 0)
                                                        <span class="block text-xs text-green-600">{{ round($diffMinutes) }} min early</span>
                                                    @endif
                                                @else
                                                    <span class="text-sm text-gray-400">--:--</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->actualTimeOut)
                                                    @php
                                                        $actualOut = \Carbon\Carbon::parse($record->actualTimeOut);
                                                        $diffOutMinutes = $actualOut->diffInMinutes($shiftOutTime, false);
                                                    @endphp
                                                    <span class="text-sm font-medium text-gray-900">{{ $actualOut->format('h:i A') }}</span>
                                                    @if($actualOut->toDateString() !== $record->workDate->format('Y-m-d'))
                                                        <span class="block text-xs text-gray-500">{{ $actualOut->format('M d') }}</span>
                                                    @endif
                                                    @if($diffOutMinutes > 0)
                                                        <span class="block text-xs text-orange-600">{{ round($diffOutMinutes) }} min early</span>
                                                    @elseif($diffOutMinutes < 0)
                                                        <span class="block text-xs text-purple-600">{{ round(abs($diffOutMinutes)) }} min overtime</span>
                                                    @endif
                                                @else
                                                    <span class="text-sm text-gray-400">--:--</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                                {{ $record->hoursWorked > 0 ? $record->formattedHoursWorked : '-' }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($record->advanceOTHours > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $record->formattedAdvanceOTHours }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($record->afterShiftOTHours > 0)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                                                        {{ $record->formattedAfterShiftOTHours }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($record->remarks)
                                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                        {{ $record->remarks === 'Late' ? 'bg-red-100 text-red-800' : 
                                                           ($record->remarks === 'Undertime' ? 'bg-orange-100 text-orange-800' :
                                                           ($record->remarks === 'No Time In' || $record->remarks === 'No Time Out' ? 'bg-yellow-100 text-yellow-800' : 
                                                           'bg-blue-100 text-blue-800')) }}">
                                                        {{ $record->remarks }}
                                                    </span>
                                                @else
                                                    @if($record->actualTimeIn && $record->actualTimeOut)
                                                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                            Complete
                                                        </span>
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="mt-6">
                            {{ $records->withQueryString()->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No attendance records</h3>
                            <p class="mt-1 text-sm text-gray-500">
                                @if(request('from_date') || request('to_date'))
                                    No records found for the selected date range.
                                @else
                                    Start recording your attendance using the DTR.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('attendance.dtr') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700">
                                    Go to DTR
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Summary Card --}}
            @if($records->count() > 0)
                @php
                    $totalHoursWorked = $records->sum('hoursWorked');
                    $totalAdvanceOT = $records->sum('advanceOTHours');
                    $totalAfterShiftOT = $records->sum('afterShiftOTHours');
                    $totalLate = $records->where('remarks', 'Late')->count();
                @endphp
                <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <p class="text-sm text-gray-500">Total Records</p>
                                <p class="text-2xl font-bold text-gray-900">{{ $records->total() }}</p>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4 text-center">
                                <p class="text-sm text-green-600">Total Regular Hours Worked</p>
                                <p class="text-2xl font-bold text-green-900">{{ number_format($totalHoursWorked, 2) }}h</p>
                            </div>
                            <div class="bg-blue-50 rounded-lg p-4 text-center">
                                <p class="text-sm text-blue-600">Total Advance OT</p>
                                <p class="text-2xl font-bold text-blue-900">{{ number_format($totalAdvanceOT, 2) }}h</p>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4 text-center">
                                <p class="text-sm text-purple-600">Total After-Shift OT</p>
                                <p class="text-2xl font-bold text-purple-900">{{ number_format($totalAfterShiftOT, 2) }}h</p>
                            </div>
                            <div class="bg-red-50 rounded-lg p-4 text-center">
                                <p class="text-sm text-red-600">Times Late</p>
                                <p class="text-2xl font-bold text-red-900">{{ $totalLate }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>
