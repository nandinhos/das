<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class Modal extends Component
{
    public function __construct(
        public string $title = '',
        public bool $show = false,
        public bool $closeOnOverlay = true,
        public ?string $footer = null,
    ) {}

    public function render()
    {
        return view('components.das.modal');
    }
}
