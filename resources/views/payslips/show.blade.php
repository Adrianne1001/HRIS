<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('payslips.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('Payslip') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            {{-- Company / Employee Info --}}
            <div class="card mb-6">
                <div class="card-body">
                    <div class="payslip-header">
                        <h3 class="payslip-company-name">FortiTech</h3>
                        <p class="payslip-period-name">{{ $period->name }}</p>
                        <p class="payslip-period-range">{{ $period->startDate->format('M d, Y') }} — {{ $period->endDate->format('M d, Y') }}</p>
                    </div>
                    <div class="form-grid-3">
                        <div>
                            <dt class="detail-term">Employee Name</dt>
                            <dd class="detail-value">{{ $employee->user->fullName }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Department</dt>
                            <dd class="detail-value">{{ $employee->department->value }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Position</dt>
                            <dd class="detail-value">{{ $employee->jobTitle->value }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Earnings --}}
            <div class="card mb-6">
                <div class="card-body">
                    <h3 class="section-title">Earnings</h3>
                    <dl class="payslip-line-items">
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Basic Pay</dt>
                            <dd class="payslip-line-amount">₱{{ number_format($record->basicPay, 2) }}</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Less: Absent Deduction</dt>
                            <dd class="payslip-line-amount-deduction">(₱{{ number_format($record->absentDeduction, 2) }})</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Less: Late Deduction</dt>
                            <dd class="payslip-line-amount-deduction">(₱{{ number_format($record->lateDeduction, 2) }})</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Less: Undertime Deduction</dt>
                            <dd class="payslip-line-amount-deduction">(₱{{ number_format($record->undertimeDeduction, 2) }})</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Overtime Pay</dt>
                            <dd class="payslip-line-amount">₱{{ number_format($record->overtimePay, 2) }}</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Night Differential Pay</dt>
                            <dd class="payslip-line-amount">₱{{ number_format($record->nightDifferentialPay, 2) }}</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Holiday Pay</dt>
                            <dd class="payslip-line-amount">₱{{ number_format($record->holidayPay, 2) }}</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Special Holiday Pay</dt>
                            <dd class="payslip-line-amount">₱{{ number_format($record->specialHolidayPay, 2) }}</dd>
                        </div>
                        <div class="payslip-line-item payslip-line-total">
                            <dt class="payslip-line-label-bold">Gross Pay</dt>
                            <dd class="payslip-line-amount-bold">₱{{ number_format($record->grossPay, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Attendance Summary --}}
            <div class="card mb-6">
                <div class="card-body">
                    <h3 class="section-title">Attendance Summary</h3>
                    <div class="form-grid-3">
                        <div>
                            <dt class="detail-term">Days Worked</dt>
                            <dd class="detail-value">{{ $record->daysWorked }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Days Absent</dt>
                            <dd class="detail-value">{{ $record->daysAbsent }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Approved Leave Days</dt>
                            <dd class="detail-value">{{ $record->approvedLeaveDays }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">OT Hours</dt>
                            <dd class="detail-value">{{ $record->overtimeHours }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">ND Hours</dt>
                            <dd class="detail-value">{{ $record->nightDifferentialHours }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Late Minutes</dt>
                            <dd class="detail-value">{{ $record->lateMinutes }}</dd>
                        </div>
                        <div>
                            <dt class="detail-term">Undertime Minutes</dt>
                            <dd class="detail-value">{{ $record->undertimeMinutes }}</dd>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mandatory Deductions --}}
            <div class="card mb-6">
                <div class="card-body">
                    <h3 class="section-title">Mandatory Deductions</h3>
                    @if($period->periodType->value === 'Second Half')
                        <dl class="payslip-line-items">
                            <div class="payslip-line-item">
                                <dt class="payslip-line-label">SSS</dt>
                                <dd class="payslip-line-amount">₱{{ number_format($record->sssEmployee, 2) }}</dd>
                            </div>
                            <div class="payslip-line-item">
                                <dt class="payslip-line-label">PhilHealth</dt>
                                <dd class="payslip-line-amount">₱{{ number_format($record->philhealthEmployee, 2) }}</dd>
                            </div>
                            <div class="payslip-line-item">
                                <dt class="payslip-line-label">Pag-IBIG</dt>
                                <dd class="payslip-line-amount">₱{{ number_format($record->pagibigEmployee, 2) }}</dd>
                            </div>
                            <div class="payslip-line-item">
                                <dt class="payslip-line-label">Withholding Tax</dt>
                                <dd class="payslip-line-amount">₱{{ number_format($record->withholdingTax, 2) }}</dd>
                            </div>
                            <div class="payslip-line-item payslip-line-total">
                                <dt class="payslip-line-label-bold">Total Mandatory Deductions</dt>
                                <dd class="payslip-line-amount-bold">₱{{ number_format($record->totalMandatoryDeductions, 2) }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="payslip-na-text">N/A — Mandatory deductions are applied on Second Half periods only.</p>
                    @endif
                </div>
            </div>

            {{-- Loan Deductions --}}
            <div class="card mb-6">
                <div class="card-body">
                    <h3 class="section-title">Loan Deductions</h3>
                    @php
                        $loanDeductions = $record->payrollDeductions->filter(fn($d) => $d->employeeLoanID !== null);
                    @endphp
                    @if($loanDeductions->isNotEmpty())
                        <dl class="payslip-line-items">
                            @foreach($loanDeductions as $deduction)
                                <div class="payslip-line-item">
                                    <dt class="payslip-line-label">{{ $deduction->description }}</dt>
                                    <dd class="payslip-line-amount">₱{{ number_format($deduction->employeeAmount, 2) }}</dd>
                                </div>
                            @endforeach
                            <div class="payslip-line-item payslip-line-total">
                                <dt class="payslip-line-label-bold">Total Loan Deductions</dt>
                                <dd class="payslip-line-amount-bold">₱{{ number_format($record->totalLoanDeductions, 2) }}</dd>
                            </div>
                        </dl>
                    @else
                        <p class="payslip-na-text">No loan deductions</p>
                    @endif
                </div>
            </div>

            {{-- Net Pay Summary --}}
            <div class="card">
                <div class="card-body">
                    <h3 class="section-title">Net Pay Summary</h3>
                    <dl class="payslip-line-items">
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Gross Pay</dt>
                            <dd class="payslip-line-amount">₱{{ number_format($record->grossPay, 2) }}</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Less: Total Mandatory Deductions</dt>
                            <dd class="payslip-line-amount-deduction">(₱{{ number_format($record->totalMandatoryDeductions, 2) }})</dd>
                        </div>
                        <div class="payslip-line-item">
                            <dt class="payslip-line-label">Less: Total Loan Deductions</dt>
                            <dd class="payslip-line-amount-deduction">(₱{{ number_format($record->totalLoanDeductions, 2) }})</dd>
                        </div>
                        <div class="payslip-line-item payslip-net-pay">
                            <dt class="payslip-net-pay-label">Net Pay</dt>
                            <dd class="payslip-net-pay-amount">₱{{ number_format($record->netPay, 2) }}</dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
