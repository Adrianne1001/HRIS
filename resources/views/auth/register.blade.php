<x-guest-layout>

    <h2 class="auth-form-title">{{ __('Create Account') }}</h2>
    <p class="auth-form-subtitle">{{ __('Join the FortiTech HRIS system') }}</p>

    <form method="POST" action="{{ route('register') }}" class="auth-form-fields">
        @csrf

        <!-- First Name + Last Name -->
        <div class="auth-name-grid">
            <div class="form-group">
                <x-input-label for="firstName" :value="__('First Name')" />
                <x-text-input id="firstName" class="form-input" type="text" name="firstName" :value="old('firstName')" required autofocus autocomplete="given-name" />
                <x-input-error :messages="$errors->get('firstName')" class="form-error" />
            </div>

            <div class="form-group">
                <x-input-label for="lastName" :value="__('Last Name')" />
                <x-text-input id="lastName" class="form-input" type="text" name="lastName" :value="old('lastName')" required autocomplete="family-name" />
                <x-input-error :messages="$errors->get('lastName')" class="form-error" />
            </div>
        </div>

        <!-- Middle Name -->
        <div class="form-group">
            <x-input-label for="middleName" :value="__('Middle Name')" />
            <x-text-input id="middleName" class="form-input" type="text" name="middleName" :value="old('middleName')" autocomplete="additional-name" />
            <x-input-error :messages="$errors->get('middleName')" class="form-error" />
        </div>

        <!-- Email Address -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="form-input" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="form-error" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="form-input" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="form-error" />
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="form-input" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="form-error" />
        </div>

        <!-- Submit -->
        <button type="submit" class="auth-submit-btn">
            {{ __('Create Account') }}
        </button>

    </form>

    <hr class="auth-form-divider">

    <p class="auth-form-footer">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="auth-form-footer-link">{{ __('Sign in') }}</a>
    </p>

</x-guest-layout>
