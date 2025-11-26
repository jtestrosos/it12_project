@props([
    'title' => null,
    'subtitle' => null,
    'footer' => null,
    'noPadding' => false,
    'headerActions' => null,
])

<div {{ $attributes->merge(['class' => 'card card-surface border-0 shadow-sm h-100']) }}>
    @if($title || $headerActions)
        <div class="card-header bg-transparent border-bottom-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-start">
            <div>
                @if($title)
                    <h5 class="card-title fw-bold mb-1">{{ $title }}</h5>
                @endif
                @if($subtitle)
                    <p class="card-subtitle text-muted small">{{ $subtitle }}</p>
                @endif
            </div>
            @if($headerActions)
                <div class="card-actions">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif

    <div class="card-body {{ $noPadding ? 'p-0' : 'p-4' }}">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="card-footer bg-transparent border-top-0 px-4 pb-4 pt-0">
            {{ $footer }}
        </div>
    @endif
</div>
