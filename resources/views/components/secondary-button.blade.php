<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-ghost']) }}>
    {{ $slot }}
</button>
