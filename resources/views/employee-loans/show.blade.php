<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('employee-loans.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('Loan Details') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            {{-- Loan Info --}}
            <div class="card mb-6">
                <div class="card-body">
                    <h3 class="section-title">Loan Information</h3>
                    <div class="form-grid-2">
                        <div>
                            <dt class="detail-term">Employee</dt>
                            <dd class="detail-value">{{ $loan->employee->user->fullName }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Loan Type</dt>
                            <dd class="detail-value">
                                @php
                                    $loanBadge = match(true) {
                                        str_contains($loan->loanType->value, 'SSS') => 'badge-blue',
                                        str_contains($loan->loanType->value, 'Pag-IBIG') => 'badge-purple',
                                        default => 'badge-yellow',
                                    };
                                @endphp
                                <span class="{{ $loanBadge }}">{{ $loan->loanType->value }}</span>
                            </dd>
                        </div>
                        <div>
                            <dt class="detail-term">Reference Number</dt>
                            <dd class="detail-value">{{ $loan->referenceNbr ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Loan Amount</dt>
                            <dd class="detail-value">₱{{ number_format($loan->loanAmount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Monthly Amortization</dt>
                            <dd class="detail-value">₱{{ number_format($loan->monthlyAmortization, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Total Paid</dt>
                            <dd class="detail-value">₱{{ number_format($loan->totalPaid, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Remaining Balance</dt>
                            <dd class="detail-value">₱{{ number_format($loan->remainingBalance, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Start Date</dt>
                            <dd class="detail-value">{{ $loan->startDate->format('M d, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">End Date</dt>
                            <dd class="detail-value">{{ $loan->endDate ? $loan->endDate->format('M d, Y') : '—' }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Status</dt>
                            <dd class="detail-value">
                                <span class="{{ $loan->isActive ? 'badge-success' : 'badge-gray' }}">
                                    {{ $loan->isActive ? 'Active' : 'Inactive' }}
                                </span>
                            </dd>
                        </div>
                        @if($loan->remarks)
                            <div class="md:col-span-2">
                                <dt class="detail-term">Remarks</dt>
                                <dd class="detail-value">{{ $loan->remarks }}</dd>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Deduction History --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title">Deduction History</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="data-table">
                        <thead class="table-header">
                            <tr>
                                <th scope="col" class="table-th">Payroll Period</th>
                                <th scope="col" class="table-th-right">Amount Deducted</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($loan->payrollDeductions as $deduction)
                                <tr class="table-row">
                                    <td class="table-cell">
                                        <div class="table-cell-text">{{ $deduction->payrollRecord->payrollPeriod->name }}</div>
                                    </td>
                                    <td class="table-cell table-cell-right">
                                        <div class="table-cell-text">₱{{ number_format($deduction->employeeAmount, 2) }}</div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="empty-state">
                                        <h3 class="empty-state-title">No deductions yet</h3>
                                        <p class="empty-state-text">Deductions will appear here once payroll is processed.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
