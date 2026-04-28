@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'auth-status-message']) }}>
        {{ $status }}
    </div>
@endif
