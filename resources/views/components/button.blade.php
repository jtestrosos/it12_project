@props([
    'type' => 'button',
    'variant' => 'primary', // primary, secondary, success, danger, warning, info, light, dark, link, ghost
    'size' => 'md', // sm, md, lg
    'block' => false,
    'disabled' => false,
    'loading' => false,
    'icon' => null,
    'iconPosition' => 'left', // left, right
])

@php
    $baseClasses = 'btn d-inline-flex align-items-center justify-content-center gap-2 transition-base';
    
    $variants = [
        'primary' => 'btn-primary text-white',
        'secondary' => 'btn-secondary text-white',
        'success' => 'btn-success text-white',
        'danger' => 'btn-danger text-white',
        'warning' => 'btn-warning text-dark',
        'info' => 'btn-info text-white',
        'light' => 'btn-light text-dark',
        'dark' => 'btn-dark text-white',
        'link' => 'btn-link text-decoration-none',
        'ghost' => 'btn-ghost text-dark hover:bg-gray-100', // Requires custom CSS
        'outline-primary' => 'btn-outline-primary',
        'outline-secondary' => 'btn-outline-secondary',
        'outline-danger' => 'btn-outline-danger',
    ];

    $sizes = [
        'sm' => 'btn-sm text-xs px-3',
        'md' => 'px-4 py-2',
        'lg' => 'btn-lg text-lg px-5',
    ];

    $classes = $baseClasses . ' ' . 
               ($variants[$variant] ?? $variants['primary']) . ' ' . 
               ($sizes[$size] ?? $sizes['md']) . ' ' . 
               ($block ? 'w-100' : '') . ' ' .
               ($disabled || $loading ? 'disabled' : '');
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => $classes]) }}
    @if($disabled || $loading) disabled @endif
>
    @if($loading)
        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
        <span class="visually-hidden">Loading...</span>
    @endif

    @if(!$loading && $icon && $iconPosition === 'left')
        <i class="{{ $icon }}"></i>
    @endif

    <span>{{ $slot }}</span>

    @if(!$loading && $icon && $iconPosition === 'right')
        <i class="{{ $icon }}"></i>
    @endif
</button>
