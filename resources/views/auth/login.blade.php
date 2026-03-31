<x-guest-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h2 class="auth-form-title">{{ __('Welcome Back') }}</h2>
    <p class="auth-form-subtitle">{{ __('Sign in to your account') }}</p>

    <form method="POST" action="{{ route('login') }}" class="auth-form-fields">
        @csrf

        <!-- Email Address -->
        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="form-input" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="form-error" />
        </div>

        <!-- Password -->
        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="form-input" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="form-error" />
        </div>

        <!-- Remember Me + Forgot Password -->
        <div class="auth-remember-row">
            <label for="remember_me" class="auth-remember-label">
                <input id="remember_me" type="checkbox" class="auth-remember-checkbox" name="remember">
                <span>{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-forgot-link">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit -->
        <button type="submit" class="auth-submit-btn">
            {{ __('Sign In') }}
        </button>

    </form>

    <hr class="auth-form-divider">

    <p class="auth-form-footer">
        {{ __("Don't have an account?") }}
        <a href="{{ route('register') }}" class="auth-form-footer-link">{{ __('Register') }}</a>
    </p>

</x-guest-layout>
