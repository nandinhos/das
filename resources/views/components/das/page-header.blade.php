<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if($title)
        <h1 class="text-2xl font-bold das-text">{{ $title }}</h1>
    @endif
    @if($subtitle)
        <p class="text-sm das-text-muted mt-1">{{ $subtitle }}</p>
    @endif
    @if($breadcrumbs)
        <nav class="mt-2">
            <ol class="flex items-center gap-2 text-sm das-text-muted">
                @foreach($breadcrumbs as $crumb)
                    <li class="flex items-center gap-2">
                        @if(!$loop->last)
                            <span>{{ $crumb }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        @else
                            <span class="das-text-secondary">{{ $crumb }}</span>
                        @endif
                    </li>
                @endforeach
            </ol>
        </nav>
    @endif
    @if($actions)
        <div class="mt-4 flex flex-wrap items-center gap-2">
            {{ $actions }}
        </div>
    @endif
</div>
