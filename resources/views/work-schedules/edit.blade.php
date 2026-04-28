<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('work-schedules.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('Edit Work Schedule') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-sm">
            <form method="POST" action="{{ route('work-schedules.update', $workSchedule) }}">
                @csrf
                @method('PUT')

                {{-- Schedule Information --}}
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Schedule Information</h3>
                        <p class="card-subtitle">Update the work schedule details.</p>
                    </div>

                    <div class="card-body">
                        {{-- Schedule Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Schedule Name')" />
                            <x-text-input id="name" name="name" type="text"
                                :value="old('name', $workSchedule->name)" required autofocus placeholder="e.g., Regular Day Shift" />
                            <x-input-error :messages="$errors->get('name')" />
                        </div>

                        {{-- Work Hours --}}
                        <div class="form-grid-2">
                            <div>
                                <x-input-label for="startTime" :value="__('Start Time')" />
                                <x-text-input id="startTime" name="startTime" type="time"
                                    :value="old('startTime', $workSchedule->startTime ? \Carbon\Carbon::parse($workSchedule->startTime)->format('H:i') : '')" required />
                                <x-input-error :messages="$errors->get('startTime')" />
                            </div>

                            <div>
                                <x-input-label for="endTime" :value="__('End Time')" />
                                <x-text-input id="endTime" name="endTime" type="time"
                                    :value="old('endTime', $workSchedule->endTime ? \Carbon\Carbon::parse($workSchedule->endTime)->format('H:i') : '')" required />
                                <x-input-error :messages="$errors->get('endTime')" />
                            </div>
                        </div>

                        {{-- Break Time --}}
                        <div class="form-grid-2">
                            <div>
                                <x-input-label for="startBreakTime" :value="__('Break Start Time')" />
                                <x-text-input id="startBreakTime" name="startBreakTime" type="time"
                                    :value="old('startBreakTime', $workSchedule->startBreakTime ? \Carbon\Carbon::parse($workSchedule->startBreakTime)->format('H:i') : '')" />
                                <p class="form-hint-xs">Optional</p>
                                <x-input-error :messages="$errors->get('startBreakTime')" />
                            </div>

                            <div>
                                <x-input-label for="endBreakTime" :value="__('Break End Time')" />
                                <x-text-input id="endBreakTime" name="endBreakTime" type="time"
                                    :value="old('endBreakTime', $workSchedule->endBreakTime ? \Carbon\Carbon::parse($workSchedule->endBreakTime)->format('H:i') : '')" />
                                <p class="form-hint-xs">Optional</p>
                                <x-input-error :messages="$errors->get('endBreakTime')" />
                            </div>
                        </div>

                        {{-- Working Days --}}
                        <div>
                            <x-input-label :value="__('Working Days')" />
                            <div class="days-row">
                                @php
                                    $days = [
                                        'Mon' => 'Monday',
                                        'Tue' => 'Tuesday',
                                        'Wed' => 'Wednesday',
                                        'Thu' => 'Thursday',
                                        'Fri' => 'Friday',
                                        'Sat' => 'Saturday',
                                        'Sun' => 'Sunday',
                                    ];
                                    $selectedDays = old('workingDays', $workSchedule->workingDaysArray);
                                @endphp
                                @foreach($days as $abbr => $full)
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="workingDays[]" value="{{ $abbr }}"
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            @checked(in_array($abbr, $selectedDays))>
                                        <span class="checkbox-label-text">{{ $full }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('workingDays')" />
                        </div>

                        {{-- Set as Default --}}
                        <div class="form-section-divider">
                            <label class="checkbox-label">
                                <input type="checkbox" name="isDefault" value="1"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @checked(old('isDefault', $workSchedule->isDefault))>
                                <span class="checkbox-label-text-bold">Set as default schedule for new employees</span>
                            </label>
                            <p class="checkbox-hint">New employees will be automatically assigned this schedule.</p>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('work-schedules.index') }}" class="btn-ghost">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Schedule
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
