<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class EmptyState extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $description = null,
        public ?string $icon = null,
        public ?string $action = null,
    ) {}

    public function render()
    {
        return view('components.das.empty-state');
    }
}
