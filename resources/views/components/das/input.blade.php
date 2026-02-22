<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium das-text-secondary mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($prefix)
            <span class="absolute left-3 top-1/2 -translate-y-1/2 das-text-muted">{{ $prefix }}</span>
        @endif
        
        <input
            type="{{ $type }}"
            id="{{ $id }}"
            {{ $attributes->class([
                'w-full das-input',
                'pl-8' => $prefix,
                'pl-3' => !$prefix,
            ])->merge([
                'placeholder' => $placeholder,
                'inputmode' => $inputmode,
            ]) }}
            @if($required) required @endif
        />
        
        @if($suffix)
            <span class="absolute right-3 top-1/2 -translate-y-1/2 das-text-muted">{{ $suffix }}</span>
        @endif
    </div>

    @error($errorName)
        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>
