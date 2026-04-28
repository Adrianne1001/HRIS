<x-guest-layout>
    <h2 class="auth-form-title">{{ __('Confirm Password') }}</h2>
    <p class="auth-form-subtitle">{{ __('This is a secure area. Please confirm your password before continuing.') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="auth-form-fields">
        @csrf

        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <button type="submit" class="auth-submit-btn">
            {{ __('Confirm') }}
        </button>
    </form>
</x-guest-layout>
