<x-app-layout>
    <x-slot name="header">
        <div class="header-row-between">
            <h2 class="header-title">
                {{ __('Employee Loans') }}
            </h2>
            <a href="{{ route('employee-loans.create') }}" class="btn-primary">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Loan
            </a>
        </div>
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
                    <form method="GET" action="{{ route('employee-loans.index') }}" class="filter-bar">
                        {{-- Search --}}
                        <div class="flex-1 lg:max-w-xs">
                            <x-input-label for="search" :value="__('Search')" />
                            <x-text-input id="search" name="search" type="text" class="mt-1 block w-full"
                                :value="request('search')" placeholder="Employee name..." />
                        </div>

                        {{-- Loan Type Filter --}}
                        <div class="flex-1 lg:max-w-[200px]">
                            <x-input-label for="loanType" :value="__('Loan Type')" />
                            <select id="loanType" name="loanType" class="form-select">
                                <option value="">All Types</option>
                                @foreach($loanTypes as $type)
                                    <option value="{{ $type->value }}" @selected(request('loanType') === $type->value)>
                                        {{ $type->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div class="flex-1 lg:max-w-[150px]">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="form-select">
                                <option value="">All</option>
                                <option value="active" @selected(request('status') === 'active')>Active</option>
                                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                            </select>
                        </div>

                        {{-- Filter & Clear Buttons --}}
                        <div class="flex items-center gap-2">
                            <button type="submit" class="btn-filter">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                                </svg>
                                Filter
                            </button>
                            @if(request()->hasAny(['search', 'loanType', 'status']))
                                <a href="{{ route('employee-loans.index') }}" class="btn-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            {{-- Loans Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Loan Type</th>
                                <th scope="col" class="table-th">Ref #</th>
                                <th scope="col" class="table-th-right">Loan Amount</th>
                                <th scope="col" class="table-th-right">Monthly Amort.</th>
                                <th scope="col" class="table-th-right">Remaining Bal.</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($loans as $loan)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <div class="table-cell-text">{{ $loan->employee->user->fullName }}</div>
                                    </td>
                                    <td class="table-cell">
                                        @php
                                            $loanBadge = match(true) {
                                                str_contains($loan->loanType->value, 'SSS') => 'badge-blue',
                                                str_contains($loan->loanType->value, 'Pag-IBIG') => 'badge-purple',
                                                default => 'badge-yellow',
                                            };
                                        @endphp
                                        <span class="{{ $loanBadge }}">{{ $loan->loanType->value }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-cell-sub">{{ $loan->referenceNbr }}</div>
                                    </td>
                                    <td class="table-cell table-cell-right">
                                        <div class="table-cell-text">₱{{ number_format($loan->loanAmount, 2) }}</div>
                                    </td>
                                    <td class="table-cell table-cell-right">
                                        <div class="table-cell-text">₱{{ number_format($loan->monthlyAmortization, 2) }}</div>
                                    </td>
                                    <td class="table-cell table-cell-right">
                                        <div class="table-cell-text">₱{{ number_format($loan->remainingBalance, 2) }}</div>
                                    </td>
                                    <td class="table-cell">
                                        <span class="{{ $loan->isActive ? 'badge-success' : 'badge-gray' }}">
                                            {{ $loan->isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('employee-loans.show', $loan) }}" class="action-view" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('employee-loans.edit', $loan) }}" class="action-edit" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('employee-loans.destroy', $loan) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this loan? This action cannot be undone.');">
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
                                    <td colspan="8" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No loans found</h3>
                                        <p class="empty-state-text">Get started by adding a new employee loan.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('employee-loans.create') }}" class="btn-primary">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Add Loan
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($loans->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $loans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
