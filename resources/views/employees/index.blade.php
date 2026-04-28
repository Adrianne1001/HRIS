<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Employees') }}
        </h2>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            {{-- Filters --}}
            <div class="card mb-6">
                <div class="card-body">
                    <form method="GET" action="{{ route('employees.index') }}" class="filter-bar">
                        {{-- Search --}}
                        <div class="flex-1 lg:max-w-xs">
                            <x-input-label for="search" :value="__('Search')" />
                            <x-text-input id="search" name="search" type="text"
                                :value="request('search')" placeholder="Name or email..." />
                        </div>

                        {{-- Department Filter --}}
                        <div class="flex-1 lg:max-w-[180px]">
                            <x-input-label for="department" :value="__('Department')" />
                            <select id="department" name="department" class="form-select">
                                <option value="">All Departments</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->value }}" @selected(request('department') === $dept->value)>
                                        {{ $dept->value }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status Filter --}}
                        <div class="flex-1 lg:max-w-[150px]">
                            <x-input-label for="status" :value="__('Status')" />
                            <select id="status" name="status" class="form-select">
                                <option value="">All Statuses</option>
                                @foreach($statuses as $status)
                                    <option value="{{ $status->value }}" @selected(request('status') === $status->value)>
                                        {{ $status->value }}
                                    </option>
                                @endforeach
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
                            @if(request()->hasAny(['search', 'department', 'status', 'type']))
                                <a href="{{ route('employees.index') }}" class="btn-secondary">
                                    Clear
                                </a>
                            @endif
                        </div>

                        {{-- Spacer --}}
                        <div class="hidden lg:block flex-1"></div>

                        {{-- Add Employee Button --}}
                        <a href="{{ route('employees.create') }}" class="btn-primary">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Add Employee
                        </a>
                    </form>
                </div>
            </div>

            {{-- Employee Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Department</th>
                                <th scope="col" class="table-th">Position</th>
                                <th scope="col" class="table-th">Type</th>
                                <th scope="col" class="table-th">Status</th>
                                <th scope="col" class="table-th">Hire Date</th>
                                <th scope="col" class="table-th-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($employees as $employee)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <div class="table-avatar-cell">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                @if($employee->profilePic)
                                                    <img class="h-10 w-10 rounded-full object-cover" src="{{ asset('storage/' . $employee->profilePic) }}" alt="{{ $employee->user->fullName }}">
                                                @else
                                                    <div class="table-avatar-placeholder">
                                                        <span class="table-avatar-initials">
                                                            {{ substr($employee->user->firstName, 0, 1) }}{{ substr($employee->user->lastName, 0, 1) }}
                                                        </span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="table-avatar-info">
                                                <div class="table-avatar-name">{{ $employee->user->fullName }}</div>
                                                <div class="table-avatar-sub">{{ $employee->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-cell-text">{{ $employee->department->value }}</div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-cell-text">{{ $employee->jobTitle->value }}</div>
                                    </td>
                                    <td class="table-cell">
                                        @php
                                            $typeBadge = match($employee->employmentType->value) {
                                                'Regular' => 'badge-blue',
                                                'Probationary' => 'badge-yellow',
                                                'Contractual' => 'badge-purple',
                                                'Temporary' => 'badge-warning',
                                                'Casual' => 'badge-gray',
                                                'Project-based' => 'badge-teal',
                                                default => 'badge-gray',
                                            };
                                        @endphp
                                        <span class="{{ $typeBadge }}">{{ $employee->employmentType->value }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="{{ $employee->employmentStatus->value === 'Active' ? 'badge-success' : 'badge-danger' }}">
                                            {{ $employee->employmentStatus->value }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-sub">{{ $employee->hireDate->format('M d, Y') }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('employees.show', $employee) }}" class="action-view" title="View">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </a>
                                            <a href="{{ route('employees.edit', $employee) }}" class="action-edit" title="Edit">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </a>
                                            <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this employee? This action cannot be undone.');">
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
                                    <td colspan="7" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No employees found</h3>
                                        <p class="empty-state-text">Get started by adding a new employee.</p>
                                        <div class="mt-6">
                                            <a href="{{ route('employees.create') }}" class="btn-primary">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                                </svg>
                                                Add Employee
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($employees->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $employees->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
