<x-app-layout>
    <x-slot name="header">
        <div class="header-row-between">
            <h2 class="header-title">
                {{ __('Attendance History') }}
            </h2>          
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            @if(!$employee)
                {{-- No Employee Record Message --}}
                <div class="card">
                    <div class="empty-state">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900">No Employee Record Found</h3>
                        <p class="mt-2 text-gray-500">Your account is not linked to an employee record. Please contact HR to set up your employee profile.</p>
                        <div class="mt-6">
                            <a href="{{ route('dashboard') }}" class="btn-primary">
                                Return to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            @else
            {{-- Filter Form --}}
            <div class="card mb-6">
                <div class="card-body">
                    <form action="{{ route('attendance.index') }}" method="GET" class="filter-bar">
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
                                <a href="{{ route('attendance.index') }}" class="btn-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Records Table --}}
            <div class="card">
                <div class="card-body">
                    @if($records->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead class="table-header">
                                    <tr>
                                        <th class="table-th">Date</th>
                                        <th class="table-th">Shift Schedule</th>
                                        <th class="table-th">Time In</th>
                                        <th class="table-th">Time Out</th>
                                        <th class="table-th">Regular Hrs Worked</th>
                                        <th class="table-th">Advance OT</th>
                                        <th class="table-th">After-Shift OT</th>
                                        <th class="table-th">Remarks</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($records as $record)
                                        <tr class="table-row">
                                            <td class="table-cell">
                                                <div class="table-cell-text">{{ \Carbon\Carbon::parse($record->workDate)->format('M d, Y') }}</div>
                                                <div class="table-cell-sub">{{ \Carbon\Carbon::parse($record->workDate)->format('l') }}</div>
                                            </td>
                                            <td class="table-cell table-cell-sub">
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
                                            <td class="table-cell">
                                                @if($record->actualTimeIn)
                                                    @php
                                                        $actualIn = \Carbon\Carbon::parse($record->actualTimeIn);
                                                        $diffMinutes = $actualIn->diffInMinutes($shiftInTime, false);
                                                    @endphp
                                                    <span class="table-cell-text">{{ $actualIn->format('h:i A') }}</span>
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
                                            <td class="table-cell">
                                                @if($record->actualTimeOut)
                                                    @php
                                                        $actualOut = \Carbon\Carbon::parse($record->actualTimeOut);
                                                        $diffOutMinutes = $actualOut->diffInMinutes($shiftOutTime, false);
                                                    @endphp
                                                    <span class="table-cell-text">{{ $actualOut->format('h:i A') }}</span>
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
                                            <td class="table-cell table-cell-text">
                                                {{ $record->hoursWorked > 0 ? $record->formattedHoursWorked : '-' }}
                                            </td>
                                            <td class="table-cell">
                                                @if($record->advanceOTHours > 0)
                                                    <span class="badge badge-ot-advance">
                                                        {{ $record->formattedAdvanceOTHours }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="table-cell">
                                                @if($record->afterShiftOTHours > 0)
                                                    <span class="badge badge-ot-after">
                                                        {{ $record->formattedAfterShiftOTHours }}
                                                    </span>
                                                @else
                                                    <span class="text-gray-400">-</span>
                                                @endif
                                            </td>
                                            <td class="table-cell">
                                                @if($record->remarks)
                                                    @php
                                                        $remarkBadge = match(true) {
                                                            $record->remarks === 'Late' => 'badge-late',
                                                            $record->remarks === 'Undertime' => 'badge-undertime',
                                                            in_array($record->remarks, ['No Time In', 'No Time Out']) => 'badge-no-record',
                                                            default => 'badge-complete',
                                                        };
                                                    @endphp
                                                    <span class="badge {{ $remarkBadge }}">{{ $record->remarks }}</span>
                                                @else
                                                    @if($record->actualTimeIn && $record->actualTimeOut)
                                                        <span class="badge badge-complete">Complete</span>
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
                        <div class="empty-state">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            <h3 class="empty-state-title">No attendance records</h3>
                            <p class="empty-state-text">
                                @if(request('from_date') || request('to_date'))
                                    No records found for the selected date range.
                                @else
                                    Start recording your attendance using the DTR.
                                @endif
                            </p>
                            <div class="mt-6">
                                <a href="{{ route('attendance.dtr') }}" class="btn-primary">
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
                <div class="card mt-6">
                    <div class="card-body">
                        <h3 class="section-title">Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="summary-card summary-card-gray">
                                <p class="summary-card-label">Total Records</p>
                                <p class="summary-card-value">{{ $records->total() }}</p>
                            </div>
                            <div class="summary-card summary-card-green">
                                <p class="summary-card-label">Total Regular Hours Worked</p>
                                <p class="summary-card-value">{{ number_format($totalHoursWorked, 2) }}h</p>
                            </div>
                            <div class="summary-card summary-card-blue">
                                <p class="summary-card-label">Total Advance OT</p>
                                <p class="summary-card-value">{{ number_format($totalAdvanceOT, 2) }}h</p>
                            </div>
                            <div class="summary-card summary-card-purple">
                                <p class="summary-card-label">Total After-Shift OT</p>
                                <p class="summary-card-value">{{ number_format($totalAfterShiftOT, 2) }}h</p>
                            </div>
                            <div class="summary-card summary-card-red">
                                <p class="summary-card-label">Times Late</p>
                                <p class="summary-card-value">{{ $totalLate }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>
