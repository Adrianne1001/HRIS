<x-app-layout>
    <x-slot name="header">
        <h2 class="header-title">{{ __('Manage Leave Balances') }}</h2>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            @if (session('success'))
                <div class="alert-success" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if (session('error'))
                <div class="alert-error" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- Filters & Allocate --}}
            <div class="card mb-6">
                <div class="card-body">
                    <div class="manage-toolbar">
                        {{-- Year filter --}}
                        <form method="GET" action="{{ route('leave-balances.manage') }}" class="filter-bar">
                            <div>
                                <x-input-label for="year" :value="__('Year')" />
                                <select id="year" name="year" class="form-select" onchange="this.form.submit()">
                                    @for($y = now()->year + 1; $y >= now()->year - 2; $y--)
                                        <option value="{{ $y }}" @selected($year == $y)>{{ $y }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div>
                                <x-input-label for="search" :value="__('Search')" />
                                <x-text-input id="search" name="search" type="text" class="form-input" :value="request('search')" placeholder="Employee name..." />
                            </div>
                            <button type="submit" class="btn-filter">Filter</button>
                        </form>

                        <div class="manage-toolbar-actions">
                            <form method="POST" action="{{ route('leave-balances.allocate') }}" onsubmit="return confirm('This will allocate default credits for all active employees for {{ $year }}. Continue?')">
                                @csrf
                                <input type="hidden" name="year" value="{{ $year }}">
                                <button type="submit" class="btn-primary">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Bulk Allocate {{ $year }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Employee Balances Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                @foreach($leaveTypes as $type)
                                    <th scope="col" class="table-th-center">{{ $type->code }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($employees as $employee)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $employee->user->fullName }}</span>
                                    </td>
                                    @foreach($leaveTypes as $type)
                                        @php
                                            $balance = $employee->leaveBalances->firstWhere('leaveTypeID', $type->id);
                                        @endphp
                                        <td class="table-cell-center">
                                            @if($balance)
                                                <span class="table-cell-text">{{ number_format($balance->remainingCredits, 1) }}</span>
                                                <span class="table-cell-sub balance-fraction-sub">/ {{ $balance->totalCredits }}</span>
                                            @else
                                                <span class="table-cell-sub">—</span>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $leaveTypes->count() + 1 }}" class="table-cell-center table-cell-sub">
                                        No employees found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                {{ $employees->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
