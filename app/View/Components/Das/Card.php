<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class Card extends Component
{
    public function __construct(
        public ?string $title = null,
        public bool $highlighted = false,
    ) {}

    public function render()
    {
        return view('components.das.card');
    }
}
