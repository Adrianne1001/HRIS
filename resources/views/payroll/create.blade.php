<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('payroll.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('Create Payroll Period') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            <form method="POST" action="{{ route('payroll.store') }}" class="space-y-6">
                @csrf

                {{-- Period Details --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Period Details
                        </h3>

                        <div class="form-grid-2">
                            {{-- Period Name --}}
                            <div>
                                <x-input-label for="name" :value="__('Period Name')" />
                                <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus placeholder="e.g. March 2026 - First Half" />
                                <x-input-error :messages="$errors->get('name')" />
                            </div>

                            {{-- Period Type --}}
                            <div>
                                <x-input-label for="periodType" :value="__('Period Type')" />
                                <select id="periodType" name="periodType" class="form-select" required>
                                    <option value="">Select Type</option>
                                    @foreach($periodTypes as $type)
                                        <option value="{{ $type->value }}" @selected(old('periodType') === $type->value)>
                                            {{ $type->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('periodType')" />
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
                                <x-text-input id="endDate" name="endDate" type="date" :value="old('endDate')" required />
                                <x-input-error :messages="$errors->get('endDate')" />
                            </div>

                            {{-- Pay Date --}}
                            <div>
                                <x-input-label for="payDate" :value="__('Pay Date')" />
                                <x-text-input id="payDate" name="payDate" type="date" :value="old('payDate')" required />
                                <x-input-error :messages="$errors->get('payDate')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('payroll.index') }}" class="btn-ghost">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Period
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
