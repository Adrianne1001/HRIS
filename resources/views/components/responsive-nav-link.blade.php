@props(['active'])

@php
$classes = ($active ?? false) ? 'responsive-nav-link-active' : 'responsive-nav-link-inactive';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
