<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class Badge extends Component
{
    public function __construct(
        public string $variant = 'primary',
    ) {}

    public function render()
    {
        return view('components.das.badge');
    }
}
