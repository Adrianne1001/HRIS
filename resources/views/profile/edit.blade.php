<x-app-layout>
    <x-slot name="header">
        <h2 class="header-title">{{ __('Profile') }}</h2>
    </x-slot>

    <div class="page-container">
        <div class="page-content space-y-6">
            <div class="card">
                <div class="card-body max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="card">
                <div class="card-body max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="card">
                <div class="card-body max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
