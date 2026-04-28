<x-app-layout>
    <x-slot name="header">
        <div class="header-row-between">
            <h2 class="header-title">
                {{ __('Payroll Periods') }}
            </h2>
            <a href="{{ route('payroll.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Period
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
                                'Draft' => 'Draft',
                                'Processing' => 'Processing',
                                'Completed' => 'Completed',
                            ];
                        @endphp
                        @foreach($tabs as $key => $label)
                            <a href="{{ route('payroll.index', ['status' => $key]) }}"
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

            {{-- Payroll Periods Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Period Name</th>
                                <th scope="col" class="table-th">Type</th>
                                <th scope="col" class="table-th">Date Range</th>
                                <th scope="col" class="table-th">Pay Date</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th">Employees</th>
                                <th scope="col" class="table-th">Total Net Pay</th>
                                <th scope="col" class="table-th-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($periods as $period)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $period->name }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="{{ $period->periodType->value === 'First Half' ? 'badge-blue' : 'badge-purple' }}">
                                            {{ $period->periodType->value }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $period->startDate->format('M d, Y') }} - {{ $period->endDate->format('M d, Y') }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-sub">{{ $period->payDate->format('M d, Y') }}</span>
                                    </td>
                                    <td class="table-cell">
                                        @php
                                            $statusBadge = match($period->status->value) {
                                                'Draft' => 'badge-yellow',
                                                'Processing' => 'badge-info',
                                                'Completed' => 'badge-success',
                                                default => 'badge-gray',
                                            };
                                        @endphp
                                        <span class="{{ $statusBadge }}">{{ $period->status->value }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $period->employeeCount }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">₱{{ number_format($period->totalNetPay, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('payroll.show', $period) }}" class="action-view" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            @if($period->status->value === 'Draft')
                                                <form action="{{ route('payroll.destroy', $period) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this payroll period? This action cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-delete" title="Delete">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No payroll periods found</h3>
                                        <p class="empty-state-text">Get started by creating a new payroll period.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('payroll.create') }}" class="btn-primary">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Create Period
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($periods->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $periods->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
