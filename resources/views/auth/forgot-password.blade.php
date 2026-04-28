<x-guest-layout>
    <h2 class="auth-form-title">{{ __('Forgot Password') }}</h2>
    <p class="auth-form-subtitle">{{ __('Enter your email and we\'ll send you a reset link.') }}</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="auth-form-fields">
        @csrf

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <button type="submit" class="auth-submit-btn">
            {{ __('Email Password Reset Link') }}
        </button>
    </form>

    <hr class="auth-form-divider">

    <p class="auth-form-footer">
        <a href="{{ route('login') }}" class="auth-form-footer-link">{{ __('Back to Sign In') }}</a>
    </p>
</x-guest-layout>
