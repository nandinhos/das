<?php

namespace App\View\Components\Das;

use Illuminate\View\Component;

class PageHeader extends Component
{
    public function __construct(
        public ?string $title = null,
        public ?string $subtitle = null,
        public ?array $breadcrumbs = null,
    ) {}

    public function render()
    {
        return view('components.das.page-header');
    }
}
