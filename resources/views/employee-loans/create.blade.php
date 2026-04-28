<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('employee-loans.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('Add Employee Loan') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            <form method="POST" action="{{ route('employee-loans.store') }}" class="space-y-6">
                @csrf

                {{-- Loan Details --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">Loan Details</h3>
                        <div class="form-grid-2">
                            {{-- Employee --}}
                            <div>
                                <x-input-label for="employeeID" :value="__('Employee')" />
                                <select id="employeeID" name="employeeID" class="form-select" required>
                                    <option value="">Select Employee</option>
                                    @foreach($employees as $employee)
                                        <option value="{{ $employee->employeeID }}" @selected(old('employeeID') == $employee->employeeID)>
                                            {{ $employee->user->fullName }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('employeeID')" />
                            </div>

                            {{-- Loan Type --}}
                            <div>
                                <x-input-label for="loanType" :value="__('Loan Type')" />
                                <select id="loanType" name="loanType" class="form-select" required>
                                    <option value="">Select Loan Type</option>
                                    @foreach($loanTypes as $type)
                                        <option value="{{ $type->value }}" @selected(old('loanType') === $type->value)>
                                            {{ $type->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('loanType')" />
                            </div>

                            {{-- Reference Number --}}
                            <div>
                                <x-input-label for="referenceNbr" :value="__('Reference Number')" />
                                <x-text-input id="referenceNbr" name="referenceNbr" type="text" :value="old('referenceNbr')" />
                                <x-input-error :messages="$errors->get('referenceNbr')" />
                            </div>

                            {{-- Loan Amount --}}
                            <div>
                                <x-input-label for="loanAmount" :value="__('Loan Amount')" />
                                <div class="salary-input-wrapper">
                                    <div class="salary-prefix">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <x-text-input id="loanAmount" name="loanAmount" type="number" step="0.01" min="0"
                                        class="pl-8" :value="old('loanAmount')" required placeholder="0.00" />
                                </div>
                                <x-input-error :messages="$errors->get('loanAmount')" />
                            </div>

                            {{-- Monthly Amortization --}}
                            <div>
                                <x-input-label for="monthlyAmortization" :value="__('Monthly Amortization')" />
                                <div class="salary-input-wrapper">
                                    <div class="salary-prefix">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <x-text-input id="monthlyAmortization" name="monthlyAmortization" type="number" step="0.01" min="0"
                                        class="pl-8" :value="old('monthlyAmortization')" required placeholder="0.00" />
                                </div>
                                <x-input-error :messages="$errors->get('monthlyAmortization')" />
                            </div>

                            {{-- Start Date --}}
                            <div>
                                <x-input-label for="startDate" :value="__('Start Date')" />
                                <x-text-input id="startDate" name="startDate" type="date" :value="old('startDate')" required />
                                <x-input-error :messages="$errors->get('startDate')" />
                            </div>

                            {{-- End Date --}}
                            <div>
                                <x-input-label for="endDate" :value="__('End Date')" />
                                <x-text-input id="endDate" name="endDate" type="date" :value="old('endDate')" />
                                <x-input-error :messages="$errors->get('endDate')" />
                            </div>

                            {{-- Remarks --}}
                            <div class="md:col-span-2">
                                <x-input-label for="remarks" :value="__('Remarks')" />
                                <textarea id="remarks" name="remarks" rows="3" class="form-select">{{ old('remarks') }}</textarea>
                                <x-input-error :messages="$errors->get('remarks')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('employee-loans.index') }}" class="btn-ghost">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Loan
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
