<div>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium das-text-secondary mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select
        id="{{ $id }}"
        {{ $attributes->class('w-full das-input appearance-none bg-no-repeat bg-right pr-10')->merge([
            'style' => "background-image: url(\"data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e\"); background-position: right 0.5rem center; background-size: 1.5em 1.5em;",
        ]) }}
        @if($required) required @endif
    >
        {{ $slot }}
    </select>

    @error($errorName)
        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
    @enderror
</div>
