<div
    x-data="{ show: {{ $show ? 'true' : 'false' }} }"
    x-show="show"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="das-modal-overlay"
    @if($closeOnOverlay) @click="show = false" @endif
>
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 translate-y-4"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-4"
        @click.stop
        class="das-modal p-6"
    >
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold das-text">{{ $title }}</h3>
            <button
                type="button"
                @click="show = false"
                class="touch-target flex items-center justify-center rounded-lg das-text-muted hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        
        <div class="mb-6 das-text-secondary">
            {{ $slot }}
        </div>
        
        @if($footer)
            <div class="flex justify-end gap-3">
                {{ $footer }}
            </div>
        @endif
    </div>
</div>
