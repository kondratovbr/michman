<?php declare(strict_types=1);

namespace App\View\Components\Layouts;

use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Html extends Component
{
    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('layouts.html');
    }
}
