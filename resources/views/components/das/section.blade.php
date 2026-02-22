<section {{ $attributes->merge(['class' => 'das-section mb-6']) }}>
    @if($title)
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-semibold das-text">{{ $title }}</h2>
                @if($subtitle)
                    <p class="text-sm das-text-muted mt-0.5">{{ $subtitle }}</p>
                @endif
            </div>
            @if($actions)
                <div class="flex items-center gap-2">{{ $actions }}</div>
            @endif
        </div>
    @endif
    {{ $slot }}
</section>
