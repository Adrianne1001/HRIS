<x-app-layout>
    <x-slot name="header">
        <div class="header-row-between">
            <h2 class="header-title">
                {{ __('Leave Requests') }}
            </h2>
            <a href="{{ route('leave-requests.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Leave Request
            </a>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            {{-- Status Tabs --}}
            <div class="card mb-6">
                <div class="border-b border-gray-200">
                    <nav class="tab-nav">
                        @php
                            $tabs = [
                                'all' => 'All',
                                'Pending' => 'Pending',
                                'Approved' => 'Approved',
                                'Rejected' => 'Rejected',
                                'Cancelled' => 'Cancelled',
                            ];
                        @endphp
                        @foreach($tabs as $key => $label)
                            <a href="{{ route('leave-requests.index', ['status' => $key]) }}"
                               class="tab-item {{ $currentStatus === $key ? 'tab-item-active' : '' }}">
                                {{ $label }}
                                <span class="tab-badge {{ $currentStatus === $key ? 'tab-badge-active' : 'tab-badge-inactive' }}">
                                    {{ $counts[$key] }}
                                </span>
                            </a>
                        @endforeach
                    </nav>
                </div>
            </div>

            {{-- Filters --}}
            <div class="card mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('leave-requests.index') }}" class="filter-bar">
                        <input type="hidden" name="status" value="{{ $currentStatus }}">

                        {{-- Search --}}
                        <div class="flex-1 lg:max-w-xs">
                            <x-input-label for="search" :value="__('Search Employee')" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full"
                                :value="request('search')" placeholder="Employee name..." />
                        </div>

                        {{-- Date From --}}
                        <div class="flex-1 lg:max-w-[180px]">
                            <x-input-label for="date_from" :value="__('From')" />
                            <x-text-input id="date_from" name="date_from" type="date" class="mt-1 block w-full"
                                :value="request('date_from')" />
                        </div>

                        {{-- Date To --}}
                        <div class="flex-1 lg:max-w-[180px]">
                            <x-input-label for="date_to" :value="__('To')" />
                            <x-text-input id="date_to" name="date_to" type="date" class="mt-1 block w-full"
                                :value="request('date_to')" />
                        </div>

                        {{-- Filter & Clear Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-filter">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'date_from', 'date_to']))
                                <a href="{{ route('leave-requests.index', ['status' => $currentStatus]) }}" class="btn-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Leave Requests Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Leave Type</th>
                                <th scope="col" class="table-th">Dates</th>
                                <th scope="col" class="table-th">Days</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th">Filed On</th>
                                <th scope="col" class="table-th-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($leaveRequests as $leaveRequest)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $leaveRequest->employee->user->fullName }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $leaveRequest->leaveType->name }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $leaveRequest->startDate->format('M d, Y') }} - {{ $leaveRequest->endDate->format('M d, Y') }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">
                                            {{ $leaveRequest->totalDays }}
                                            @if($leaveRequest->isHalfDay)
                                                <span class="table-cell-sub">(Half-day)</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        @php
                                            $statusBadge = match($leaveRequest->status->value) {
                                                'Pending' => 'badge-yellow',
                                                'Approved' => 'badge-success',
                                                'Rejected' => 'badge-danger',
                                                'Cancelled' => 'badge-gray',
                                                default => 'badge-gray',
                                            };
                                        @endphp
                                        <span class="{{ $statusBadge }}">{{ $leaveRequest->status->value }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-sub">{{ $leaveRequest->created_at->format('M d, Y') }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('leave-requests.show', $leaveRequest) }}" class="action-view" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                        </svg>
                                        <h3 class="empty-state-title">No leave requests found</h3>
                                        <p class="empty-state-text">Get started by filing a new leave request.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('leave-requests.create') }}" class="btn-primary">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                New Leave Request
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($leaveRequests->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $leaveRequests->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
