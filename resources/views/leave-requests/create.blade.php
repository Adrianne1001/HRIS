<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('leave-requests.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('New Leave Request') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            <form method="POST" action="{{ route('leave-requests.store') }}" class="space-y-6">
                @csrf

                {{-- Leave Request Details --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Leave Request Details
                        </h3>

                        <div class="form-grid-2">
                            {{-- Leave Type --}}
                            <div>
                                <x-input-label for="leaveTypeID" :value="__('Leave Type')" />
                                <select id="leaveTypeID" name="leaveTypeID" class="form-select" required>
                                    <option value="">Select Leave Type</option>
                                    @foreach($leaveTypes as $type)
                                        @php
                                            $balance = $balances->get($type->id);
                                            $remaining = $balance ? $balance->remainingCredits : 0;
                                        @endphp
                                        <option value="{{ $type->id }}" @selected(old('leaveTypeID') == $type->id)>
                                            {{ $type->name }} ({{ $remaining }} days available)
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('leaveTypeID')" />
                            </div>

                            {{-- Spacer for grid alignment --}}
                            <div></div>

                            {{-- Start Date --}}
                            <div>
                                <x-input-label for="startDate" :value="__('Start Date')" />
                                <x-text-input id="startDate" name="startDate" type="date" class="mt-1 block w-full" :value="old('startDate')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('startDate')" />
                            </div>

                            {{-- End Date --}}
                            <div>
                                <x-input-label for="endDate" :value="__('End Date')" />
                                <x-text-input id="endDate" name="endDate" type="date" class="mt-1 block w-full" :value="old('endDate')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('endDate')" />
                            </div>

                            {{-- Half-day Toggle --}}
                            <div x-data="{ isHalfDay: {{ old('isHalfDay') ? 'true' : 'false' }} }" class="md:col-span-2">
                                <label class="checkbox-label">
                                    <input type="hidden" name="isHalfDay" value="0">
                                    <input type="checkbox" name="isHalfDay" value="1" x-model="isHalfDay"
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('isHalfDay'))>
                                    <span class="checkbox-label-text-bold">Half-day Leave</span>
                                </label>
                                <div x-show="isHalfDay" x-cloak class="mt-3">
                                    <x-input-label for="halfDayPeriod" :value="__('Period')" />
                                    <select id="halfDayPeriod" name="halfDayPeriod" class="form-select mt-1" style="max-width: 200px;">
                                        <option value="">Select Period</option>
                                        <option value="AM" @selected(old('halfDayPeriod') === 'AM')>AM (Morning)</option>
                                        <option value="PM" @selected(old('halfDayPeriod') === 'PM')>PM (Afternoon)</option>
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('halfDayPeriod')" />
                                </div>
                            </div>

                            {{-- Reason --}}
                            <div class="md:col-span-2">
                                <x-input-label for="reason" :value="__('Reason')" />
                                <textarea id="reason" name="reason" rows="4" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>{{ old('reason') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('reason')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Leave Balance Summary --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Your Leave Balances
                        </h3>
                        <div class="overflow-x-auto">
                            <table class="data-table">
                                <thead class="table-header">
                                    <tr>
                                        <th scope="col" class="table-th">Leave Type</th>
                                        <th scope="col" class="table-th">Total</th>
                                        <th scope="col" class="table-th">Used</th>
                                        <th scope="col" class="table-th">Pending</th>
                                        <th scope="col" class="table-th">Available</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($leaveTypes as $type)
                                        @php $balance = $balances->get($type->id); @endphp
                                        <tr class="table-row">
                                            <td class="table-cell"><span class="table-cell-text">{{ $type->name }}</span></td>
                                            <td class="table-cell"><span class="table-cell-text">{{ $balance ? $balance->totalCredits : '0.00' }}</span></td>
                                            <td class="table-cell"><span class="table-cell-text">{{ $balance ? $balance->usedCredits : '0.00' }}</span></td>
                                            <td class="table-cell"><span class="table-cell-text">{{ $balance ? $balance->pendingCredits : '0.00' }}</span></td>
                                            <td class="table-cell"><span class="table-cell-text font-semibold">{{ $balance ? number_format($balance->remainingCredits, 2) : '0.00' }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('leave-requests.index') }}" class="btn-ghost">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Submit Request
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
