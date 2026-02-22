<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class Select extends Component
{
    public function __construct(
        public ?string $id = null,
        public ?string $label = null,
        public ?string $errorName = null,
        public bool $required = false,
    ) {}

    public function render()
    {
        return view('components.das.select');
    }
}
