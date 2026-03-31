<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('payroll.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ $period->name }}
            </h2>
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

            {{-- Summary Stats --}}
            <div class="stat-grid">
                {{-- Total Gross Pay --}}
                <div class="stat-card">
                    <div class="flex items-center">
                        <div class="stat-card-icon-indigo">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="stat-card-body">
                            <p class="stat-card-label">Total Gross Pay</p>
                            <p class="stat-card-value">₱{{ number_format($period->totalGrossPay, 2) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Total Deductions --}}
                <div class="stat-card">
                    <div class="flex items-center">
                        <div class="stat-card-icon-yellow">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="stat-card-body">
                            <p class="stat-card-label">Total Deductions</p>
                            <p class="stat-card-value">₱{{ number_format($period->totalDeductions, 2) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Total Net Pay --}}
                <div class="stat-card">
                    <div class="flex items-center">
                        <div class="stat-card-icon-green">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="stat-card-body">
                            <p class="stat-card-label">Total Net Pay</p>
                            <p class="stat-card-value">₱{{ number_format($period->totalNetPay, 2) }}</p>
                        </div>
                    </div>
                </div>

                {{-- Employer Contributions --}}
                <div class="stat-card">
                    <div class="flex items-center">
                        <div class="stat-card-icon-purple">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div class="stat-card-body">
                            <p class="stat-card-label">Employer Contributions</p>
                            <p class="stat-card-value">₱{{ number_format($period->totalEmployerContributions, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Period Info Card --}}
            <div class="card mb-6">
                <div class="card-body">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-4">
                            @php
                                $statusBadge = match($period->status->value) {
                                    'Draft' => 'badge-yellow',
                                    'Processing' => 'badge-info',
                                    'Completed' => 'badge-success',
                                    default => 'badge-gray',
                                };
                            @endphp
                            <span class="{{ $statusBadge }}">{{ $period->status->value }}</span>
                            <span class="{{ $period->periodType->value === 'First Half' ? 'badge-blue' : 'badge-purple' }}">
                                {{ $period->periodType->value }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-6 text-sm text-gray-600">
                            <div>
                                <span class="font-medium text-gray-900">Date Range:</span>
                                {{ $period->startDate->format('M d, Y') }} - {{ $period->endDate->format('M d, Y') }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Pay Date:</span>
                                {{ $period->payDate->format('M d, Y') }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900">Employees:</span>
                                {{ $period->employeeCount }}
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            @if($period->status->value === 'Draft')
                                <form method="POST" action="{{ route('payroll.process', $period) }}">
                                    @csrf
                                    <button type="submit" class="btn-success" onclick="return confirm('Are you sure you want to process this payroll? This will compute payroll records for all active employees.')">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        Process Payroll
                                    </button>
                                </form>
                            @elseif($period->status->value === 'Processing')
                                <form method="POST" action="{{ route('payroll.complete', $period) }}">
                                    @csrf
                                    <button type="submit" class="btn-primary" onclick="return confirm('Are you sure you want to mark this payroll as completed? This action cannot be undone.')">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Mark as Completed
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Payroll Records Table --}}
            <div class="card mb-6">
                <div class="card-body">
                    <h3 class="section-title">
                        <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Payroll Records
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Employee</th>
                                <th scope="col" class="table-th">Basic Pay</th>
                                <th scope="col" class="table-th">OT Pay</th>
                                <th scope="col" class="table-th">ND Pay</th>
                                <th scope="col" class="table-th">Holiday Pay</th>
                                <th scope="col" class="table-th">Gross Pay</th>
                                <th scope="col" class="table-th">Mandatory Ded.</th>
                                <th scope="col" class="table-th">Loan Ded.</th>
                                <th scope="col" class="table-th">Net Pay</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($records as $record)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <span class="table-cell-text">{{ $record->employee->user->fullName }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">₱{{ number_format($record->basicPay, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">₱{{ number_format($record->overtimePay, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">₱{{ number_format($record->nightDifferentialPay, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">₱{{ number_format($record->holidayPay + $record->specialHolidayPay, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text font-semibold">₱{{ number_format($record->grossPay, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">₱{{ number_format($record->totalMandatoryDeductions, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text">₱{{ number_format($record->totalLoanDeductions, 2) }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-text font-semibold">₱{{ number_format($record->netPay, 2) }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No payroll records yet</h3>
                                        <p class="empty-state-text">Process this payroll period to generate records.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($records->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>

            {{-- Government Contribution Summary --}}
            @if($contributionSummary)
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            Government Contribution Summary
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="table-th">Contribution Type</th>
                                        <th scope="col" class="table-th">Employee Share</th>
                                        <th scope="col" class="table-th">Employer Share</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr class="table-row">
                                        <td class="table-cell">
                                            <span class="table-cell-text">SSS</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text">₱{{ number_format($contributionSummary['sss']['employee'] ?? 0, 2) }}</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text">₱{{ number_format($contributionSummary['sss']['employer'] ?? 0, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr class="table-row">
                                        <td class="table-cell">
                                            <span class="table-cell-text">PhilHealth</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text">₱{{ number_format($contributionSummary['philhealth']['employee'] ?? 0, 2) }}</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text">₱{{ number_format($contributionSummary['philhealth']['employer'] ?? 0, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr class="table-row">
                                        <td class="table-cell">
                                            <span class="table-cell-text">Pag-IBIG</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text">₱{{ number_format($contributionSummary['pagibig']['employee'] ?? 0, 2) }}</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text">₱{{ number_format($contributionSummary['pagibig']['employer'] ?? 0, 2) }}</span>
                                        </td>
                                    </tr>
                                    <tr class="table-row font-semibold">
                                        <td class="table-cell">
                                            <span class="table-cell-text font-semibold">Total</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text font-semibold">₱{{ number_format(
                                                ($contributionSummary['sss']['employee'] ?? 0) +
                                                ($contributionSummary['philhealth']['employee'] ?? 0) +
                                                ($contributionSummary['pagibig']['employee'] ?? 0), 2
                                            ) }}</span>
                                        </td>
                                        <td class="table-cell">
                                            <span class="table-cell-text font-semibold">₱{{ number_format(
                                                ($contributionSummary['sss']['employer'] ?? 0) +
                                                ($contributionSummary['philhealth']['employer'] ?? 0) +
                                                ($contributionSummary['pagibig']['employer'] ?? 0), 2
                                            ) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
