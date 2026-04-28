<section>
    <header>
        <h2 class="card-title">{{ __('Profile Information') }}</h2>
        <p class="card-description">{{ __("Update your account's profile information and email address.") }}</p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="form-group">
            <x-input-label for="firstName" :value="__('First Name')" />
            <x-text-input id="firstName" name="firstName" type="text" :value="old('firstName', $user->firstName)" required autofocus autocomplete="given-name" />
            <x-input-error :messages="$errors->get('firstName')" />
        </div>

        <div class="form-group">
            <x-input-label for="middleName" :value="__('Middle Name')" />
            <x-text-input id="middleName" name="middleName" type="text" :value="old('middleName', $user->middleName)" autocomplete="additional-name" />
            <x-input-error :messages="$errors->get('middleName')" />
        </div>

        <div class="form-group">
            <x-input-label for="lastName" :value="__('Last Name')" />
            <x-text-input id="lastName" name="lastName" type="text" :value="old('lastName', $user->lastName)" required autocomplete="family-name" />
            <x-input-error :messages="$errors->get('lastName')" />
        </div>

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" name="email" type="email" :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div>
                    <p class="form-hint">
                        {{ __('Your email address is unverified.') }}
                        <button form="send-verification" class="auth-forgot-link">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                    @if (session('status') === 'verification-link-sent')
                        <p class="auth-status-message mt-2">
                            {{ __('A new verification link has been sent to your email address.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions justify-start">
            <x-primary-button>{{ __('Save') }}</x-primary-button>
            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)" class="form-hint">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
