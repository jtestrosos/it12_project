@props([
    'title' => 'No data found',
    'description' => 'There is no data to display at the moment.',
    'icon' => 'fa-folder-open',
    'action' => null,
])

<div class="text-center py-5">
    <div class="mb-3 text-muted opacity-50">
        <i class="fas {{ $icon }} fa-4x"></i>
    </div>
    <h5 class="fw-bold text-muted mb-2">{{ $title }}</h5>
    <p class="text-muted mb-4" style="max-width: 400px; margin: 0 auto;">{{ $description }}</p>
    
    @if($action)
        <div>
            {{ $action }}
        </div>
    @endif
</div>
