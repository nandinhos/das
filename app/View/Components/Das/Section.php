<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class Section extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?string $actions = null,
    ) {}

    public function render()
    {
        return view('components.das.section');
    }
}
