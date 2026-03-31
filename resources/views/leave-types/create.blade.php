<x-app-layout>
    <x-slot name="header">
        <div class="header-row">
            <a href="{{ route('leave-types.index') }}" class="back-link">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <h2 class="header-title">
                {{ __('Add Leave Type') }}
            </h2>
        </div>
    </x-slot>

    <div class="page-container">
        <div class="page-content-md">
            <form method="POST" action="{{ route('leave-types.store') }}" class="space-y-6">
                @csrf

                {{-- Leave Type Details --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Leave Type Details
                        </h3>

                        <div class="form-grid-3">
                            {{-- Name --}}
                            <div>
                                <x-input-label for="name" :value="__('Name')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required autofocus />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            {{-- Code --}}
                            <div>
                                <x-input-label for="code" :value="__('Code')" />
                                <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code')" required maxlength="50" />
                                <x-input-error class="mt-2" :messages="$errors->get('code')" />
                            </div>

                            {{-- Default Credits --}}
                            <div>
                                <x-input-label for="defaultCredits" :value="__('Default Credits')" />
                                <x-text-input id="defaultCredits" name="defaultCredits" type="number" step="0.01" class="mt-1 block w-full" :value="old('defaultCredits')" required />
                                <x-input-error class="mt-2" :messages="$errors->get('defaultCredits')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Settings --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Settings
                        </h3>

                        <div class="form-grid-3">
                            {{-- Description --}}
                            <div class="md:col-span-3">
                                <x-input-label for="description" :value="__('Description')" />
                                <textarea id="description" name="description" rows="3" class="form-select">{{ old('description') }}</textarea>
                                <x-input-error class="mt-2" :messages="$errors->get('description')" />
                            </div>

                            {{-- Max Consecutive Days --}}
                            <div>
                                <x-input-label for="maxConsecutiveDays" :value="__('Max Consecutive Days')" />
                                <x-text-input id="maxConsecutiveDays" name="maxConsecutiveDays" type="number" class="mt-1 block w-full" :value="old('maxConsecutiveDays')" />
                                <x-input-error class="mt-2" :messages="$errors->get('maxConsecutiveDays')" />
                            </div>

                            {{-- Gender --}}
                            <div>
                                <x-input-label for="gender" :value="__('Gender Restriction')" />
                                <select id="gender" name="gender" class="form-select">
                                    <option value="">All Genders</option>
                                    @foreach($genders as $gender)
                                        <option value="{{ $gender->value }}" @selected(old('gender') === $gender->value)>
                                            {{ $gender->value }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('gender')" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Options --}}
                <div class="card">
                    <div class="card-body">
                        <h3 class="section-title">
                            <svg class="w-5 h-5 inline-block mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Options
                        </h3>

                        <div class="space-y-4">
                            {{-- Is Active --}}
                            <div>
                                <label class="checkbox-label">
                                    <input type="hidden" name="isActive" value="0">
                                    <input type="checkbox" name="isActive" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('isActive', true))>
                                    <span class="checkbox-label-text-bold">Active</span>
                                </label>
                                <p class="checkbox-hint">Whether this leave type is available for requests</p>
                            </div>

                            {{-- Is Paid --}}
                            <div>
                                <label class="checkbox-label">
                                    <input type="hidden" name="isPaid" value="0">
                                    <input type="checkbox" name="isPaid" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('isPaid', true))>
                                    <span class="checkbox-label-text-bold">Paid</span>
                                </label>
                                <p class="checkbox-hint">Whether employees are paid during this leave</p>
                            </div>

                            {{-- Requires Document --}}
                            <div>
                                <label class="checkbox-label">
                                    <input type="hidden" name="requiresDocument" value="0">
                                    <input type="checkbox" name="requiresDocument" value="1" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500" @checked(old('requiresDocument', false))>
                                    <span class="checkbox-label-text-bold">Requires Document</span>
                                </label>
                                <p class="checkbox-hint">Whether supporting documents are required for this leave type</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="form-actions">
                    <a href="{{ route('leave-types.index') }}" class="btn-ghost">
                        Cancel
                    </a>
                    <x-primary-button>
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Create Leave Type
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
