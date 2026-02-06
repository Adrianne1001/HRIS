<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center">
            <a href="{{ route('employees.index') }}" class="text-gray-500 hover:text-gray-700 mr-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Add New Employee') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('employees.store') }}" class="space-y-6">
                @csrf

                {{-- Account Information --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Account Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- First Name --}}
                            <div>
                                <x-input-label for="firstName" :value="__('First Name')" />
                                <x-text-input id="firstName" name="firstName" type="text" class="mt-1 block w-full" :value="old('firstName')" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('firstName')" />
                            </div>

                            {{-- Middle Name --}}
                            <div>
                                <x-input-label for="middleName" :value="__('Middle Name')" />
                                <x-text-input id="middleName" name="middleName" type="text" class="mt-1 block w-full" :value="old('middleName')" />
                                <x-input-error class="mt-2" :messages="$errors->get('middleName')" />
                            </div>

                            {{-- Last Name --}}
                            <div>
                                <x-input-label for="lastName" :value="__('Last Name')" />
                                <x-text-input id="lastName" name="lastName" type="text" class="mt-1 block w-full" :value="old('lastName')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('lastName')" />
                            </div>

                            {{-- Email --}}
                            <div class="md:col-span-3">
                                <x-input-label for="email" :value="__('Email Address')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            {{-- Password --}}
                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password')" />
                            </div>

                            {{-- Confirm Password --}}
                            <div class="md:col-span-2">
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                                <x-input-error class="mt-2" :messages="$errors->get('password_confirmation')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal Information --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"/>
                            </svg>
                            Personal Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Date of Birth --}}
                            <div>
                                <x-input-label for="dateOfBirth" :value="__('Date of Birth')" />
                                <x-text-input id="dateOfBirth" name="dateOfBirth" type="date" class="mt-1 block w-full" :value="old('dateOfBirth')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('dateOfBirth')" />
                            </div>

                            {{-- Gender --}}
                            <div>
                                <x-input-label for="gender" :value="__('Gender')" />
                                <select id="gender" name="gender" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Gender</option>
                                    @foreach($genders as $gender)
                                        <option value="{{ $gender->value }}" @selected(old('gender') === $gender->value)>
                                            {{ $gender->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                            </div>

                            {{-- Marital Status --}}
                            <div>
                                <x-input-label for="maritalStatus" :value="__('Marital Status')" />
                                <select id="maritalStatus" name="maritalStatus" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Status</option>
                                    @foreach($maritalStatuses as $status)
                                        <option value="{{ $status->value }}" @selected(old('maritalStatus') === $status->value)>
                                            {{ $status->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('maritalStatus')" />
                            </div>

                            {{-- Phone Number --}}
                            <div>
                                <x-input-label for="phoneNbr" :value="__('Phone Number')" />
                                <x-text-input id="phoneNbr" name="phoneNbr" type="tel" class="mt-1 block w-full" :value="old('phoneNbr')" required placeholder="+63 9XX XXX XXXX" />
                                <x-input-error class="mt-2" :messages="$errors->get('phoneNbr')" />
                            </div>

                            {{-- Address --}}
                            <div class="md:col-span-2">
                                <x-input-label for="address" :value="__('Address')" />
                                <x-text-input id="address" name="address" type="text" class="mt-1 block w-full" :value="old('address')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('address')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Employment Information --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Employment Information
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            {{-- Department --}}
                            <div>
                                <x-input-label for="department" :value="__('Department')" />
                                <select id="department" name="department" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->value }}" @selected(old('department') === $department->value)>
                                            {{ $department->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('department')" />
                            </div>

                            {{-- Job Title / Position --}}
                            <div>
                                <x-input-label for="jobTitle" :value="__('Position')" />
                                <select id="jobTitle" name="jobTitle" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Position</option>
                                    @foreach($positions as $position)
                                        <option value="{{ $position->value }}" @selected(old('jobTitle') === $position->value)>
                                            {{ $position->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('jobTitle')" />
                            </div>

                            {{-- Hire Date --}}
                            <div>
                                <x-input-label for="hireDate" :value="__('Hire Date')" />
                                <x-text-input id="hireDate" name="hireDate" type="date" class="mt-1 block w-full" :value="old('hireDate', date('Y-m-d'))" required />
                                <x-input-error class="mt-2" :messages="$errors->get('hireDate')" />
                            </div>

                            {{-- Employment Status --}}
                            <div>
                                <x-input-label for="employmentStatus" :value="__('Employment Status')" />
                                <select id="employmentStatus" name="employmentStatus" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Status</option>
                                    @foreach($employmentStatuses as $status)
                                        <option value="{{ $status->value }}" @selected(old('employmentStatus', 'Active') === $status->value)>
                                            {{ $status->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('employmentStatus')" />
                            </div>

                            {{-- Employment Type --}}
                            <div>
                                <x-input-label for="employmentType" :value="__('Employment Type')" />
                                <select id="employmentType" name="employmentType" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                    <option value="">Select Type</option>
                                    @foreach($employmentTypes as $type)
                                        <option value="{{ $type->value }}" @selected(old('employmentType') === $type->value)>
                                            {{ $type->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('employmentType')" />
                            </div>

                            {{-- Basic Monthly Salary --}}
                            <div>
                                <x-input-label for="basicMonthlySalary" :value="__('Basic Monthly Salary')" />
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <span class="text-gray-500 sm:text-sm">₱</span>
                                    </div>
                                    <x-text-input id="basicMonthlySalary" name="basicMonthlySalary" type="number" step="0.01" min="0" 
                                        class="block w-full pl-8" :value="old('basicMonthlySalary')" required placeholder="0.00" />
                                </div>
                                <x-input-error class="mt-2" :messages="$errors->get('basicMonthlySalary')" />
                            </div>

                            {{-- Work Schedule --}}
                            <div class="md:col-span-3">
                                <x-input-label for="workScheduleID" :value="__('Work Schedule')" />
                                <select id="workScheduleID" name="workScheduleID" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                    <option value="">No Schedule Assigned</option>
                                    @foreach($workSchedules as $schedule)
                                        <option value="{{ $schedule->id }}" @selected(old('workScheduleID', $defaultScheduleId) == $schedule->id)>
                                            {{ $schedule->name }}
                                            ({{ \Carbon\Carbon::parse($schedule->startTime)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->endTime)->format('h:i A') }})
                                            @if($schedule->isDefault) — Default @endif
                                        </option>
                                    @endforeach
                                </select>
                                @if($workSchedules->isEmpty())
                                    <p class="mt-1 text-sm text-gray-500">
                                        No work schedules available. <a href="{{ route('work-schedules.create') }}" class="text-indigo-600 hover:text-indigo-800">Create one</a>
                                    </p>
                                @endif
                                <x-input-error class="mt-2" :messages="$errors->get('workScheduleID')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Emergency Contact --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4 pb-2 border-b border-gray-200">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            Emergency Contact
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Emergency Contact Name --}}
                            <div>
                                <x-input-label for="emergencyContactName" :value="__('Contact Name')" />
                                <x-text-input id="emergencyContactName" name="emergencyContactName" type="text" class="mt-1 block w-full" :value="old('emergencyContactName')" />
                                <x-input-error class="mt-2" :messages="$errors->get('emergencyContactName')" />
                            </div>

                            {{-- Emergency Contact Phone --}}
                            <div>
                                <x-input-label for="emergencyContactPhoneNbr" :value="__('Contact Phone Number')" />
                                <x-text-input id="emergencyContactPhoneNbr" name="emergencyContactPhoneNbr" type="tel" class="mt-1 block w-full" :value="old('emergencyContactPhoneNbr')" placeholder="+63 9XX XXX XXXX" />
                                <x-input-error class="mt-2" :messages="$errors->get('emergencyContactPhoneNbr')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end gap-4">
                    <a href="{{ route('employees.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Employee
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
