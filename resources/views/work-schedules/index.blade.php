<x-app-layout>
    <x-slot name="header">
        <h2 class="header-title">
            {{ __('Work Schedules') }}
        </h2>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert-success" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Error Message --}}
            @if (session('error'))
                <div class="alert-error" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filters --}}
            <div class="card mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('work-schedules.index') }}" class="filter-bar">
                        {{-- Search --}}
                        <div class="flex-1 lg:max-w-sm">
                            <x-input-label for="search" :value="__('Search')" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full" 
                                :value="request('search')" placeholder="Schedule name..." />
                        </div>

                        {{-- Filter & Clear Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-filter">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                                Search
                            </button>
                            @if(request()->hasAny(['search']))
                                <a href="{{ route('work-schedules.index') }}" class="btn-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>

                        {{-- Spacer --}}
                        <div class="hidden lg:block flex-1"></div>

                        {{-- Add Work Schedule Button --}}
                        <a href="{{ route('work-schedules.create') }}" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Schedule
                        </a>
                    </form>
                </div>
            </div>

            {{-- Work Schedules Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">
                                    Schedule Name
                                </th>
                                <th scope="col" class="table-th">
                                    Work Hours
                                </th>
                                <th scope="col" class="table-th">
                                    Break Time
                                </th>
                                <th scope="col" class="table-th">
                                    Working Days
                                </th>
                                <th scope="col" class="table-th">
                                    Employees
                                </th>
                                <th scope="col" class="table-th-right">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($workSchedules as $schedule)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <div class="table-avatar-cell">
                                            <div class="icon-box-sm bg-indigo-100">
                                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <div class="table-avatar-info">
                                                <div class="table-avatar-name">
                                                    {{ $schedule->name }}
                                                    @if($schedule->isDefault)
                                                        <span class="badge-default">Default</span>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-cell-text">
                                            {{ \Carbon\Carbon::parse($schedule->startTime)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->endTime)->format('h:i A') }}
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-cell-text">
                                            @if($schedule->startBreakTime && $schedule->endBreakTime)
                                                {{ \Carbon\Carbon::parse($schedule->startBreakTime)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->endBreakTime)->format('h:i A') }}
                                            @else
                                                <span class="text-gray-400 italic">No break</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="flex flex-wrap gap-1">
                                            @php
                                                $allDays = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
                                                $workingDays = $schedule->workingDaysArray;
                                            @endphp
                                            @foreach($allDays as $day)
                                                <span class="{{ in_array($day, $workingDays) ? 'day-pill-active' : 'day-pill-inactive' }}">
                                                    {{ $day }}
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <span class="badge-count">
                                            {{ $schedule->employees()->count() }} assigned
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            @if(!$schedule->isDefault)
                                                <form action="{{ route('work-schedules.set-default', $schedule) }}" method="POST" class="inline">
                                                    @csrf
                                                    <button type="submit" class="action-success" title="Set as Default">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @else
                                                <span class="action-success-static" title="Current Default">
                                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
                                                    </svg>
                                                </span>
                                            @endif
                                            <a href="{{ route('work-schedules.show', $schedule) }}" class="action-view" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('work-schedules.edit', $schedule) }}" class="action-edit" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('work-schedules.destroy', $schedule) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this work schedule?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-delete" title="Delete">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No work schedules found</h3>
                                        <p class="empty-state-text">Get started by creating a new work schedule.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('work-schedules.create') }}" class="btn-primary">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Add Schedule
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($workSchedules->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $workSchedules->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
