<x-guest-layout>
    <h2 class="auth-form-title">{{ __('Verify Email') }}</h2>
    <p class="auth-form-subtitle">{{ __('Please verify your email address by clicking the link we sent you.') }}</p>

    @if (session('status') == 'verification-link-sent')
        <div class="auth-status-message">
            {{ __('A new verification link has been sent to your email address.') }}
        </div>
    @endif

    <div class="auth-form-fields">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button type="submit" class="auth-submit-btn">
                {{ __('Resend Verification Email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <p class="auth-form-footer">
                <button type="submit" class="auth-form-footer-link">
                    {{ __('Log Out') }}
                </button>
            </p>
        </form>
    </div>
</x-guest-layout>
