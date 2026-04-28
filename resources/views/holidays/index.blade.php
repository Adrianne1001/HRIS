<x-app-layout>
    <x-slot name="header">
        <div class="header-row-between">
            <h2 class="header-title">
                {{ __('Holidays') }}
            </h2>
            <a href="{{ route('holidays.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Holiday
            </a>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            {{-- Year Filter --}}
            <div class="card mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('holidays.index') }}" class="filter-bar">
                        <div class="flex-1 lg:max-w-[150px]">
                            <x-input-label for="year" :value="__('Year')" />
                            <select id="year" name="year" class="form-select" onchange="this.form.submit()">
                                @foreach($years as $year)
                                    <option value="{{ $year }}" @selected($currentYear == $year)>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Holidays Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Name</th>
                                <th scope="col" class="table-th">Date</th>
                                <th scope="col" class="table-th">Type</th>
                                <th scope="col" class="table-th-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($holidays as $holiday)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <div class="table-cell-text">{{ $holiday->name }}</div>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-sub">{{ $holiday->date->format('M d, Y') }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="{{ $holiday->holidayType->value === 'Regular Holiday' ? 'badge-danger' : 'badge-yellow' }}">
                                            {{ $holiday->holidayType->value }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('holidays.edit', $holiday) }}" class="action-edit" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('holidays.destroy', $holiday) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this holiday?');">
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
                                    <td colspan="4" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No holidays found</h3>
                                        <p class="empty-state-text">Add holidays for this year to get started.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('holidays.create') }}" class="btn-primary">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Add Holiday
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($holidays->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $holidays->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
