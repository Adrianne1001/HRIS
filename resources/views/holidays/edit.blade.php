<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('holidays.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('Edit Holiday') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            <form method="POST" action="{{ route('holidays.update', $holiday) }}" class="space-y-6"
                x-data="{ holidayDate: '{{ old('date', $holiday->date->format('Y-m-d')) }}' }">
                @csrf
                @method('PUT')

                {{-- Holiday Details --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">Holiday Details</h3>
                        <div class="form-grid-2">
                            {{-- Name --}}
                            <div>
                                <x-input-label for="name" :value="__('Holiday Name')" />
                                <x-text-input id="name" name="name" type="text" :value="old('name', $holiday->name)" required autofocus />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            {{-- Date --}}
                            <div>
                                <x-input-label for="date" :value="__('Date')" />
                                <x-text-input id="date" name="date" type="date"
                                    x-model="holidayDate" required />
                                <x-input-error :messages="$errors->get('date')" />
                            </div>

                            {{-- Holiday Type --}}
                            <div>
                                <x-input-label for="holidayType" :value="__('Holiday Type')" />
                                <select id="holidayType" name="holidayType" class="form-select" required>
                                    <option value="">Select Type</option>
                                    @foreach($holidayTypes as $type)
                                        <option value="{{ $type->value }}" @selected(old('holidayType', $holiday->holidayType->value) === $type->value)>
                                            {{ $type->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('holidayType')" />
                            </div>

                            {{-- Year --}}
                            <div>
                                <x-input-label for="year" :value="__('Year')" />
                                <x-text-input id="year" name="year" type="number"
                                    x-bind:value="holidayDate ? new Date(holidayDate).getFullYear() : '{{ old('year', $holiday->year) }}'"
                                    required min="2000" max="2099" />
                                <x-input-error :messages="$errors->get('year')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('holidays.index') }}" class="btn-ghost">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update Holiday
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
