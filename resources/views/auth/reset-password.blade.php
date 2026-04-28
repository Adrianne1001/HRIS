<x-guest-layout>
    <h2 class="auth-form-title">{{ __('Reset Password') }}</h2>
    <p class="auth-form-subtitle">{{ __('Choose a new password for your account.') }}</p>

    <form method="POST" action="{{ route('password.store') }}" class="auth-form-fields">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="form-group">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form-group">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="form-group">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <button type="submit" class="auth-submit-btn">
            {{ __('Reset Password') }}
        </button>
    </form>
</x-guest-layout>
