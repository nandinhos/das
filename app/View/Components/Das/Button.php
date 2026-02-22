<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class Button extends Component
{
    public function __construct(
        public string $variant = 'primary',
        public string $type = 'button',
        public bool $loading = false,
        public bool $disabled = false,
    ) {}

    public function variantClass(): string
    {
        return match ($this->variant) {
            'primary' => 'das-btn-primary',
            'secondary' => 'das-btn-secondary',
            'danger' => 'das-btn-danger',
            'ghost' => 'das-btn-ghost',
            default => 'das-btn-primary',
        };
    }

    public function render()
    {
        return view('components.das.button');
    }
}
