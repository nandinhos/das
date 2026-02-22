<div class="das-empty">
    @if($icon)
        <div class="mx-auto h-12 w-12 das-text-muted mb-4">
            {{ $icon }}
        </div>
    @endif
    <h3 class="text-base font-medium das-text mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-sm das-text-muted">{{ $description }}</p>
    @endif
    @if($action)
        <div class="mt-4">{{ $action }}</div>
    @endif
</div>
