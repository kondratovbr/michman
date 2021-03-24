<?php declare(strict_types=1);

namespace App\View\Components\Box;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Form extends Component
{
    public function __construct(
        public string|null $method = null,
        public bool $withFiles = false,
    )
    {}

    /**
     * Render the component.
     */
    public function render(): View
    {
        return view('components.box.form');
    }
}
