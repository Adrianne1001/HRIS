<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Leave Types') }}
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
                    <form method="GET" action="{{ route('leave-types.index') }}" class="filter-bar">
                        {{-- Search --}}
                        <div class="flex-1 lg:max-w-xs">
                            <x-input-label for="search" :value="__('Search')" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full"
                                :value="request('search')" placeholder="Name or code..." />
                        </div>

                        {{-- Filter & Clear Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-filter">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                            @if(request()->hasAny(['search']))
                                <a href="{{ route('leave-types.index') }}" class="btn-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>

                        {{-- Spacer --}}
                        <div class="hidden lg:block flex-1"></div>

                        {{-- Add Leave Type Button --}}
                        <a href="{{ route('leave-types.create') }}" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Leave Type
                        </a>
                    </form>
                </div>
            </div>

            {{-- Leave Types Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Name</th>
                                <th scope="col" class="table-th">Code</th>
                                <th scope="col" class="table-th">Default Credits</th>
                                <th scope="col" class="table-th">Paid</th>
                                <th scope="col" class="table-th">Active</th>
                                <th scope="col" class="table-th-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($leaveTypes as $leaveType)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $leaveType->name }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-sub">{{ $leaveType->code }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $leaveType->defaultCredits }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="{{ $leaveType->isPaid ? 'badge-success' : 'badge-danger' }}">
                                            {{ $leaveType->isPaid ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="{{ $leaveType->isActive ? 'badge-success' : 'badge-danger' }}">
                                            {{ $leaveType->isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('leave-types.show', $leaveType) }}" class="action-view" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('leave-types.edit', $leaveType) }}" class="action-edit" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('leave-types.destroy', $leaveType) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this leave type? This action cannot be undone.');">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No leave types found</h3>
                                        <p class="empty-state-text">Get started by adding a new leave type.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('leave-types.create') }}" class="btn-primary">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Add Leave Type
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($leaveTypes->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $leaveTypes->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
