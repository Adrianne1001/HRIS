<x-app-layout>
    <x-slot name="header">
        <h2 class="header-title">
            {{ __('My Payslips') }}
        </h2>
    </x-slot>

    <div class="page-container">
        <div class="page-content">
            {{-- Payslips Table --}}
            <div class="card">
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Period</th>
                                <th scope="col" class="table-th">Type</th>
                                <th scope="col" class="table-th">Pay Date</th>
                                <th scope="col" class="table-th-right">Net Pay</th>
                                <th scope="col" class="table-th-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($payslips as $payslip)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <div class="table-cell-text">{{ $payslip->payrollPeriod->name }}</div>
                                    </td>
                                    <td class="table-cell">
                                        @php
                                            $typeBadge = $payslip->payrollPeriod->periodType->value === 'First Half' ? 'badge-blue' : 'badge-purple';
                                        @endphp
                                        <span class="{{ $typeBadge }}">{{ $payslip->payrollPeriod->periodType->value }}</span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="table-cell-sub">{{ $payslip->payrollPeriod->payDate->format('M d, Y') }}</span>
                                    </td>
                                    <td class="table-cell table-cell-right">
                                        <div class="table-cell-text">₱{{ number_format($payslip->netPay, 2) }}</div>
                                    </td>
                                    <td class="table-cell">
                                        <div class="table-actions">
                                            <a href="{{ route('payslips.show', $payslip) }}" class="action-view" title="View">
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
                                    <td colspan="5" class="empty-state">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <h3 class="empty-state-title">No payslips found</h3>
                                        <p class="empty-state-text">Your payslips will appear here once payroll has been processed.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($payslips->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $payslips->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
