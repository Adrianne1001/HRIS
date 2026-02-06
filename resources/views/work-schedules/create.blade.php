<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('work-schedules.index') }}" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Work Schedule') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ route('work-schedules.store') }}">
                @csrf

                {{-- Schedule Information --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Schedule Information</h3>
                        <p class="mt-1 text-sm text-gray-600">Define the work schedule details.</p>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Schedule Name --}}
                        <div>
                            <x-input-label for="name" :value="__('Schedule Name')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" 
                                :value="old('name')" required autofocus placeholder="e.g., Regular Day Shift" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        {{-- Work Hours --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="startTime" :value="__('Start Time')" />
                                <x-text-input id="startTime" name="startTime" type="time" class="mt-1 block w-full" 
                                    :value="old('startTime', '08:00')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('startTime')" />
                            </div>

                            <div>
                                <x-input-label for="endTime" :value="__('End Time')" />
                                <x-text-input id="endTime" name="endTime" type="time" class="mt-1 block w-full" 
                                    :value="old('endTime', '17:00')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('endTime')" />
                            </div>
                        </div>

                        {{-- Break Time --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="startBreakTime" :value="__('Break Start Time')" />
                                <x-text-input id="startBreakTime" name="startBreakTime" type="time" class="mt-1 block w-full" 
                                    :value="old('startBreakTime', '12:00')" />
                                <p class="mt-1 text-xs text-gray-500">Optional</p>
                                <x-input-error class="mt-2" :messages="$errors->get('startBreakTime')" />
                            </div>

                            <div>
                                <x-input-label for="endBreakTime" :value="__('Break End Time')" />
                                <x-text-input id="endBreakTime" name="endBreakTime" type="time" class="mt-1 block w-full" 
                                    :value="old('endBreakTime', '13:00')" />
                                <p class="mt-1 text-xs text-gray-500">Optional</p>
                                <x-input-error class="mt-2" :messages="$errors->get('endBreakTime')" />
                            </div>
                        </div>

                        {{-- Working Days --}}
                        <div>
                            <x-input-label :value="__('Working Days')" />
                            <div class="mt-3 flex flex-wrap gap-4">
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
                                    $defaultDays = old('workingDays', ['Mon', 'Tue', 'Wed', 'Thu', 'Fri']);
                                @endphp
                                @foreach($days as $abbr => $full)
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" name="workingDays[]" value="{{ $abbr }}" 
                                            class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            @checked(in_array($abbr, $defaultDays))>
                                        <span class="ml-2 text-sm text-gray-700">{{ $full }}</span>
                                    </label>
                                @endforeach
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('workingDays')" />
                        </div>

                        {{-- Set as Default --}}
                        <div class="pt-4 border-t border-gray-200">
                            <label class="inline-flex items-center">
                                <input type="checkbox" name="isDefault" value="1" 
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    @checked(old('isDefault'))>
                                <span class="ml-2 text-sm font-medium text-gray-700">Set as default schedule for new employees</span>
                            </label>
                            <p class="mt-1 text-sm text-gray-500 ml-6">New employees will be automatically assigned this schedule.</p>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex items-center justify-end mt-6 gap-4">
                    <a href="{{ route('work-schedules.index') }}" class="inline-flex items-center px-4 py-2.5 bg-white border border-gray-300 rounded-lg font-semibold text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Schedule
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
