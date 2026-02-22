<div {{ $attributes->merge(['class' => 'das-card p-6']) }}>
    @if($title || $actions)
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold das-text">{{ $title }}</h3>
            <div>{{ $actions }}</div>
        </div>
    @endif
    {{ $slot }}
</div>
