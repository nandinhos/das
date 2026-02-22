<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class Input extends Component
{
    public function __construct(
        public ?string $id = null,
        public ?string $label = null,
        public string $type = 'text',
        public ?string $placeholder = null,
        public ?string $prefix = null,
        public ?string $suffix = null,
        public ?string $errorName = null,
        public bool $required = false,
        public ?string $inputmode = null,
    ) {}

    public function render()
    {
        return view('components.das.input');
    }
}
